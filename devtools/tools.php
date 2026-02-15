<?php


function dd_1($input)   {
                            echo '<pre>';
                            var_dump($input);
                            echo '</pre>';
                            die();
                        }

function dd_2($input1, $input2) {
                                    echo '<pre>First input: <br>';
                                    var_dump($input1);
                                    echo '<br>';
                                    echo 'Second input: <br>';
                                    var_dump($input2);
                                    echo '<br>';
                                    echo '</pre>';
                                    die();
                                }           
                    
function dd_3($input1, $input2, $input3)    {
                                                echo '<pre>First input: <br>';
                                                var_dump($input1);
                                                echo '<br>';
                                                echo 'Second input: <br>';
                                                var_dump($input2);
                                                echo '<br>';
                                                echo 'Third input: <br>';
                                                var_dump($input3);
                                                echo '<br>';
                                                echo '</pre>';
                                                die();
                                            }  
                                            
function dd_4($input1, $input2, $input3, $input4)   {
                                                        echo '<pre>First input: <br>';
                                                        var_dump($input1);
                                                        echo '<br>';
                                                        echo 'Second input: <br>';
                                                        var_dump($input2);
                                                        echo '<br>';
                                                        echo 'Third input: <br>';
                                                        var_dump($input3);
                                                        echo '<br>';
                                                        echo 'Fourth input: <br>';
                                                        var_dump($input4);
                                                        echo '<br>';
                                                        echo '</pre>';
                                                        die();
                                                    }   
?>