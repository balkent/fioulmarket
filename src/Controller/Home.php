<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Home extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request)
    {
        $I=0;
        $ls = array();
        //recupere liens flux rss avec images
        try {
            $c = curl_init();
            curl_setopt_array($c, Array(CURLOPT_URL => 'http://www.commitstrip.com/en/feed/',CURLOPT_RETURNTRANSFER => TRUE,));
            $d = curl_exec($c);curl_close($c);
            $x = simplexml_load_string($d, 'SimpleXMLElement', LIBXML_NOCDATA);
            $c=$x->channel;
            $n= count($x->channel->item);
            for($I=1; $I<$n;$I++){$h=$c->item[$I]->link;;${"ls"}[$I]=(string)$h[0];}
            for($I=1; $I<count($x->channel->item);$I++){
                if(!!substr_count((string)$c->item[$I]->children("content", true), 'jpg')<0){${"ls"}[$I] = "";}
                if(!!substr_count((string)$c->item[$I]->children("content", true), 'JPG')<0){${"ls"}[$I] = "";}
                if(!!substr_count((string)$c->item[$I]->children("content", true), 'GIF')<0){${"ls"}[$I] = "";}
                if(!!substr_count((string)$c->item[$I]->children("content", true), 'gif')<0){${"ls"}[$I] = "";}
                if(!!substr_count((string)$c->item[$I]->children("content", true), 'PNG')<0){${"ls"}[$I] = "";}
                if(!!substr_count((string)$c->item[$I]->children("content", true), '.png')<0){${"ls"}[$I] = "";}
            }
        } catch (\Exception $e) {
            // do nothing
        }

        //recpere liens api json avec image
        $j="";
        $h = @fopen("https://newsapi.org/v2/top-headlines?country=us&apiKey=c782db1cd730403f88a544b75dc2d7a0", "r");
        while ($b = fgets($h, 4096)) {$j.=$b;}
        $j=json_decode($j);
        for($II=$I+1; $II<count($j->articles);$II++){
            if($j->articles[$II]->urlToImage=="" || empty($j->articles[$II]->urlToImage) || strlen($j->articles[$II]->urlToImage)==0){continue;}
            $h=$j->articles[$II]->url;
            ${"ls2"}[$II]=$h;
        }

        //on fait un de doublonnage
        foreach($ls as $k=>$v){
            if(empty($f))$f=array();
            if($this->doublon($ls,$ls2)==false) $f[$k]=$v;
        }
        foreach($ls2 as $k2=>$v2){
            if(empty($f))$f=array();
            if($this->doublon($ls2,$ls)==false) $f[$k2]=$v2;
        }

        //recupere dans chaque url l'image
        $j=0;
        $images=array();
        while($j<count($f)){if(isset($f[$j])) {
            try {$images[] = $this->recupereimagedanspage($f[$j]);} catch (\Exception $e) { /* erreur */ }
        } $j++;}

        return $this->render('default/index.html.twig', array('images' => $images));
    }

    private function recupereimagedanspage($l){
        if(strstr($l, "commitstrip.com"))
        {
            $doc = new \DomDocument();
            @$doc->loadHTMLFile($l);
            $xpath = new \DomXpath($doc);
            $xq = $xpath->query('//img[contains(@class,"size-full")]/@src');
            $src=$xq[0]->value;

            return $src;
        }
        else
        {
            $doc = new \DomDocument();
            @$doc->loadHTMLFile($l);
            $xpath = new \DomXpath($doc);
            $xq = $xpath->query('//img/@src');
            $src=$xq[0]->value;

            return $src;
        }
    }

    private function doublon($t1,$t2){
        foreach($t1 as $k1=>$v1){
            $doublon=0;
            foreach($t2 as $v2){if($v2==$v1){$doublon=1;}}
        }
        return $doublon;
    }
}