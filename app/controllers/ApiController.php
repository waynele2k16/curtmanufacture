<?php
/**
 * @author    Wayne Le
 */
class ApiController extends ActionController
{
    public function indexAction()
    {
        $primeNumber = new PrimeNumber();
        $primeNumbers = $primeNumber->getPrimeNumbers();
        header('Content-type: application/json');
        echo json_encode($primeNumbers);
        exit();
    }
}