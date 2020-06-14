<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\creditSimulator;

class CreditSimulatorController extends Controller
{
    public function getInstitutions() {
        try {
            $json_data = json_decode(file_get_contents('../resources/jsons/instituicoes.json'));
            return response()->json([$json_data, "status" => 200], 200);            
            
        } catch (\Exception $e) {
            return response()->json(["message" => "Não foi possível realizar a leitura de instituições", "status" => 404, "error" => $e->getMessage()], 404);  
        }
    }

    public function getInstitutionsFeels() {
        try {
            $json_data = json_decode(file_get_contents('../resources/jsons/taxas_instituicoes.json'));
            return response()->json([$json_data, "status" => 200], 200);           
            
        } catch (\Exception $e) {
            return response()->json(["message" => "Não foi possível realizar a leitura de taxas de instituições", "status" => 404, "error" => $e->getMessage()], 404);  
        }  
    }

    public function getAgreements() {
        try {
            $json_data = json_decode(file_get_contents('../resources/jsons/convenios.json'));
            return response()->json([$json_data, "status" => 200], 200);           
            
        } catch (\Exception $e) {
            return response()->json(["message" => "Não foi possível realizar a leitura de convênios", "status" => 404, "error" => $e->getMessage()], 404);  
        }   
    }

    public function creditSimulator(Request $request) {
        $payload = json_decode($request->payload);
        

        $rules = [
            'valor' => 'numeric|min:1',
            'parcelas' => 'numeric',
            'convenios' => 'array',
            'instituicoes' => 'array',
        ];

        $messages = [
            'valor.numeric' => 'O valor deve ser numérico.',
            'valor.min' => 'O valor não não corresponde ao mínimo requerido (não deve ser menor ou igual a 0)',
            'parcelas.numeric' => 'O campo parcelas deve ser numérico.',
            'convenios.array' => 'O campo convênios deve ser um array de valores.',
            'instituicoes.array' => 'O campo instituições deve ser um array de valores.',
        ]; 


        if(is_null($payload)) {
            return response()->json(['message' => 'Formato inválido', 'errors' => "Dados de entrada são nulos ou tem um formato inválido", 'status' => 400], 400);            
        }



        $validator = \Validator::make(get_object_vars($payload), $rules, $messages);
        
        if ($validator->fails()) {
           return response()->json(["errors" => $validator->errors(), "status" => 422], 422);
        }

        $obj_parameters = $this->validatesReportedParameters($payload);

        if(is_object($obj_parameters) == false) {
            return response()->json(['message' => 'Parâmetros inválidos', 'errors' => $obj_parameters, 'status' => 400], 400);    
        }

        try {
            $result = $this->runCreditSimulator($obj_parameters);

            if(empty($result)) {
                return response()->json(['message' => 'Não temos nenhuma opção de crédito disponível com os parâmetros informados. Tente novamente.', 'status' => 200]);
            }

            return response()->json($result, 200);            
               
           } catch (\Exception $e) {
               return response()->json(["message" => "Não foi possível realizar a simulação de crédito.", "errors" => $e->getMessage(), "status" => 500], 500);
           }  
        
        }


    protected function validatesReportedParameters($payload)
    {
        $simulator = new creditSimulator();
        $payload_params = array_keys(get_object_vars($payload));

        if(in_array('valor', $payload_params) == false) {
            $errors[] = ("O parâmetro: 'valor' é obrigatório para realizar a simulação de crédito.");
        }

        // Validate whether informed parameters are valid to perform the simulation

            foreach ($payload_params as $key => $param) {
                if(property_exists($simulator, $param)){
                    $simulator->$param = $payload->$param;
                } else {
                    $errors[] = ("O parâmetro: '$param' não existe.");
                }
            }

            if (isset($errors)) {
                return $errors;
            } else {
                return $simulator;
            }
                  
    }

    private function calculateInstitutionsFees($result_array, $obj_institution, $loan_value) {
        $parcel_value = $loan_value * $obj_institution->coeficiente;

        $result_array[$obj_institution->instituicao][] = 
            (object) [
                    'taxa' => $obj_institution->taxaJuros,
                    'parcelas' => $obj_institution->parcelas,
                    'valor_parcela' => number_format($parcel_value, 2, '.', ''),
                    'convenio' => $obj_institution->convenio,
                ];

        return $result_array;
    }     
        
    private function runCreditSimulator($obj_credit_simulator) {
        $institution_fees = json_decode(file_get_contents('../resources/jsons/taxas_instituicoes.json'));

        $array_response = [];

        if(isset($obj_credit_simulator->convenios)) {
            $array_obj_convenios = [];

            for ($i=0; $i < sizeof($obj_credit_simulator->convenios); $i++) { 
                $array_obj_convenios += array_merge((array) $obj_credit_simulator->convenios[$i]);
            }
        }

        if(isset($obj_credit_simulator->instituicoes)) {        
            $array_obj_instituicoes = [];

            for ($i=0; $i < sizeof($obj_credit_simulator->instituicoes); $i++) { 
                $array_obj_instituicoes += array_merge((array) $obj_credit_simulator->instituicoes[$i]);
            }
        }

        foreach ($institution_fees as $institution) {

            // Search conditional
            switch ($obj_credit_simulator) {
                case isset($obj_credit_simulator->valor, $obj_credit_simulator->instituicoes, $obj_credit_simulator->convenios, $obj_credit_simulator->parcelas):
                    // // Parameters reported : value, intitutions, agreements and parcels
                    if(array_key_exists($institution->instituicao, $array_obj_instituicoes) && (array_key_exists($institution->convenio, $array_obj_convenios)) && ($institution->parcelas == $obj_credit_simulator->parcelas)) {
                        $array_response = array_merge($this->calculateInstitutionsFees($array_response, $institution, $obj_credit_simulator->valor));                        
                    } else {
                        continue;
                    }
                    break;

                case isset($obj_credit_simulator->valor, $obj_credit_simulator->instituicoes, $obj_credit_simulator->convenios):
                    // Parameters reported : value, intitutions and agreements
                    if(array_key_exists($institution->instituicao, $array_obj_instituicoes) && array_key_exists($institution->convenio, $array_obj_convenios)) {
                        $array_response = array_merge($this->calculateInstitutionsFees($array_response, $institution, $obj_credit_simulator->valor));                        
                    } else {
                        continue;
                    }

                    break;    

                case isset($obj_credit_simulator->valor, $obj_credit_simulator->instituicoes, $obj_credit_simulator->parcelas):
                    // Parameters reported : value, intitutions and parcels
                    if(array_key_exists($institution->instituicao, $array_obj_instituicoes) && ($institution->parcelas == $obj_credit_simulator->parcelas)) {
                        $array_response = array_merge($this->calculateInstitutionsFees($array_response, $institution, $obj_credit_simulator->valor));                        
                    } else {
                        continue;
                    }
                    break;  

                case isset($obj_credit_simulator->valor, $obj_credit_simulator->convenios, $obj_credit_simulator->parcelas):
                    // Parameters reported : value, agreements and parcels
                    if(array_key_exists($institution->convenio, $array_obj_convenios) && ($institution->parcelas == $obj_credit_simulator->parcelas)) {
                        $array_response = array_merge($this->calculateInstitutionsFees($array_response, $institution, $obj_credit_simulator->valor));                        
                    } else {
                        continue;
                    }
                    break;

                case isset($obj_credit_simulator->valor, $obj_credit_simulator->instituicoes):
                    // Parameters reported : value and institutions
                    if(array_key_exists($institution->instituicao, $array_obj_instituicoes)) {
                        $array_response = array_merge($this->calculateInstitutionsFees($array_response, $institution, $obj_credit_simulator->valor));                        
                    } else {
                        continue;
                    }

                    break;                    

                case isset($obj_credit_simulator->valor, $obj_credit_simulator->convenios):
                    // Parameters reported : value and agreements
                    if(array_key_exists($institution->convenio, $array_obj_convenios)) {
                        $array_response = array_merge($this->calculateInstitutionsFees($array_response, $institution, $obj_credit_simulator->valor));                        
                    } else {
                        continue;
                    }

                    break;                       

                case isset($obj_credit_simulator->valor, $obj_credit_simulator->parcelas):
                    // Parameters reported: value and parcels
                    if($institution->parcelas == $obj_credit_simulator->parcelas) {
                        $array_response = array_merge($this->calculateInstitutionsFees($array_response, $institution, $obj_credit_simulator->valor));                        
                    } else {
                        continue;
                    }

                    break;                                                                                                  
                
                default:
                    // Parameters reported: only value
                    $array_response = array_merge($this->calculateInstitutionsFees($array_response, $institution, $obj_credit_simulator->valor));

                    break;
            }

        }

        return $array_response;
    }        
}
