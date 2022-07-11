<?php
// src/Controller/ProductController.php
namespace App\Controller;

use SimpleXMLElement;
use App\Entity\Currency;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// ...

class ProductController extends AbstractController
{
    /**
     * @Route("/Currency")
     */
    public function number(ManagerRegistry $doctrine): Response
    {
        $curl = curl_init('http://api.nbp.pl/api/exchangerates/tables/a?format=xml');
        curl_setopt_array($curl, Array(
            CURLOPT_RETURNTRANSFER => TRUE,
        ));
        $data = curl_exec($curl);
        curl_close($curl);
        $xml = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
        $array = json_decode(json_encode((array)$xml), TRUE);
        $rates = $array['ExchangeRatesTable']['Rates']['Rate'];
        foreach($rates as $key => $value)
        {
            $entityManager= $doctrine->getManager();
            $currency = $value['Currency'];
            $code = $value['Code'];
            $mid = $value['Mid'];
            /* $entity = new Currency();
            $entity -> setName($currency);
            $entity -> setCurrencyCode($code);
            $entity -> setExchangeRate($mid);
            $entityManager->persist($entity); */
            echo $currency."<br>"; 
        }
        return $this->render('currency.html.twig', [
            'currency' => $currency,
            'code' => $code,
            'mid' => $mid
        ]);
    }
}
?>