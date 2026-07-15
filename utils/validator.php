<?php

function validate($rules, $data = null){

    $data = $data ?? getJson();
    if(isEmpty($rules) || isEmpty($data)) return [];

    $errors = [];

    foreach($rules as $field => $rulesString){
        $ruleArr = explode("|", $rulesString);
        $value = $data[$field] ?? "";

        foreach($ruleArr as $rule){
            $rule = trim(strtolower($rule));
            if(isEmpty($rule)) continue;

            if($rule === "required" && isEmpty($value)){
                $errors[$field] = "$field is required!";
                break;
            }

            if($rule === "username"){

                $invalidChars = [];
                if(preg_match("/^[^A-Za-z_]/", $value, $matches)){
                    $errors[$field] = "First character of $field must be a letter or underscore (_), not " . implode(", ", $matches);
                    break;
                }

                for($i = 0; $i < strlen($value); $i++){
                    if(preg_match("/[^A-Za-z0-9_\-.]/", $value[$i])){
                        $invalidChars[] = $value[$i];
                    }
                }

                if(!isEmpty($invalidChars)){
                    $invalidChars = implode(", ", array_unique($invalidChars));
                    $errors[$field] = "Invalid characters used: ({$invalidChars}), allowed are: (A-z, 0-9, _, -, .)";
                    break;
                }

                if(preg_match("/[^A-Za-z0-9_]$/", $value, $matches)){
                    $errors[$field] = "Last character of $field must be a letter or underscore (_), not " . implode(", ", $matches);
                    break;
                }
            }

            if($rule === "email" && !preg_match("/^[A-Za-z_][A-Za-z0-9\.\-_\+]*@(?:[A-Za-z0-9]+\.){1,}[A-Za-z]+$/", $value)){
                $errors[$field] = "The email format is not valid!";
                break;
            }

            if(str_starts_with($rule, "min:")){
                $min = (int) explode(":", $rule)[1];
                if(strlen($value) < $min){
                    $errors[$field] = "$field has to be atleast $min characters long!";
                    break;
                }
            }

            if(str_starts_with($rule, "max:")){
                $max = (int) explode(":", $rule)[1];
                if(strlen($value) > $max){
                    $errors[$field] = "$field has to be atmost $max characters long!";
                    break;
                }
            }

            if(str_starts_with($rule, "range:")){
                $range = explode("-", str_replace("range:", "", $rule));
                $min = $range[0];
                $max = $range[1];
                if(strlen($value) < $min){
                    $errors[$field] = "$field has to be atleast $min characters long!";
                    break;
                }
                if(strlen($value) > $max){
                    $errors[$field] = "$field has to be atmost $max characters long!";
                    break;
                }
            }

            if(str_starts_with($rule, "same:")){
                $otherField = explode(":", $rule)[1];

                if(!isset($data[$otherField])){
                    $errors[$field] = "$otherField field is missing for comparison with $field!";
                    continue;
                }

                if($value !== $data[$otherField]){
                    $errors[$field] = "$field doesn't match with $otherField!";
                    break;
                }
            }

            if(str_starts_with($rule, "different:")){
                $otherField = explode(":", $rule)[1];

                if(!isset($data[$otherField])){
                    $errors[$field] = "$otherField field is missing for comparison with $field!";
                    continue;
                }

                if($value === $data[$otherField]){
                    $errors[$field] = "$field shouldn't be same as $otherField!";
                    break;
                }
            }

            // here Unique function has to be defined by the user according to their needs like mongo Db or whatever, so.... commenting it right now, maybe would make it an interface, soon!
            
            // if($rule === "unique"){
            //     $result = isUnique("users", [$field => $value], "", "");
            //     if(!isset($result["isUnique"])) return $result;
                
            //     $isUnique = $result["isUnique"];
            //     if(!$isUnique){
            //         $errors[$field] = "This $field already exists!";
            //         break;
            //     }
            // }
            
        }
    }

    if(!isEmpty($errors)){
        return buildResponse($errors, 422, "errors");
    }

    return $data;
}



?>