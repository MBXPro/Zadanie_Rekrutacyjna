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
    public function download(ManagerRegistry $doctrine): Response
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
        $i = 0;
        $tab = [];
        echo "<table>";
        foreach($rates as $key => $value)
        {
            $entityManager= $doctrine->getManager();
            $currency = $value['Currency'];
            $code = $value['Code'];
            $mid = $value['Mid'];

            if (!array_key_exists($key,$array))
                {
                    $tab[$i][0] = $currency;
                    $tab[$i][1] = $code;
                    $tab[$i][2] = $mid;
                    echo " <td>
                    <tr> ".$tab[$i][0]." </tr>
                    <tr> ".$tab[$i][1]." </tr>
                    <tr> ".$tab[$i][2]." </tr> </br>
                    ";
                }
            /* $entity = new Currency();
            $entity -> setName($currency);
            $entity -> setCurrencyCode($code);
            $entity -> setExchangeRate($mid);
            $entityManager->persist($entity); */
            $i+=1;
        }
        echo "</table>";
        return $this->render('currency.html.twig');
        
    }
    
    public function upload($currency, $code, $mid): Response
    {
        
    }
}
?>