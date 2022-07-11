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
        function upload(int $key,string $currency,string $code,float $mid,int $i)
        {
            $tab[$i][0] = $key;
                    $tab[$i][1] = $currency;
                    $tab[$i][2] = $code;
                    $tab[$i][3] = $mid;
                    echo " <td>
                    <tr> ".$tab[$i][0]." </tr>
                    <tr> ".$tab[$i][1]." </tr>
                    <tr> ".$tab[$i][2]." </tr> 
                    <tr> ".$tab[$i][3]." </tr> </br>
                    ";
            /* $entity = new Currency();
            $entity -> setName($currency);
            $entity -> setCurrencyCode($code);
            $entity -> setExchangeRate($mid);
            $entityManager->persist($entity); 
            
            Polecenie do dodania do bazy danych (z działajacym połączeniem):
            ...$ins_query="INSERT INTO `Currency` (`id`, `name`, `currency_code`, `exchange_rate`) 
            VALUES (NULL, '$currency', '$code', '$mid');";
            if(mysqli_query($conn,$ins_query);...
            */
        }
        function update(string $currency,float $mid,$i)
        {
                    $tab[$i][1] = $currency;
                    $tab[$i][3] = $mid;
                    echo " <td>
                    <tr> ".$tab[$i][0]." </tr>
                    <tr> ".$tab[$i][1]." </tr>
                    <tr> ".$tab[$i][2]." </tr> 
                    <tr> ".$tab[$i][3]." </tr> </br>
                    ";
                    /* Polecenie do dodania do bazy danych (z działajacym połączeniem):
            ...$upt_query="UPDATE `Currency` SET `name`='$currency', `currency_code`='$code',`exchange_rate`='$mid';
            mysqli_query($conn,$upt_query);...
            */
        }
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

            if (!array_key_exists($key,$array) || $value['id']==$i)
            {
                upload($key,$currency,$code,$mid,$i); 
            }
            else 
            {
                update($currency,$mid,$i);
            }

            $i+=1;
        }
        echo "</table>";
        return $this->render('currency.html.twig');
        
    }
    

}
?>