<?php

declare(strict_types=1);

namespace App\Service;

class Image
{
    public function run(): array
    {
        $ls = $this->getImageByRSS('http://www.commitstrip.com/en/feed/');
        $ls2 = $this->getImageByAPI();

        // dump($ls);
        // dump($ls2);
        // exit();

        //on fait un de doublonnage
        foreach ($ls as $k => $v) {
            if (empty($f)) $f = array();
            if ($this->doublon($ls, $ls2) == false) $f[$k] = $v;
        }
        foreach ($ls2 as $k2 => $v2) {
            if (empty($f)) $f = array();
            if ($this->doublon($ls2, $ls) == false) $f[$k2] = $v2;
        }

        //recupere dans chaque url l'image
        $j = 0;
        $images = array();
        while ($j < count($f)) {
            if (isset($f[$j])) {
                try {
                    $images[] = $this->recupereimagedanspage($f[$j]);
                } catch (\Exception $e) { /* erreur */
                }
            }
            $j++;
        }

        return $images;
    }

    /**
     * recupere liens flux rss avec images
     */
    public function getImageByRSS(string $url): array
    {
        $ls = array();
        $xmlElement = new \SimpleXMLElement($url, LIBXML_NOCDATA, true);
        $xmlItems = $xmlElement->channel->item;

        foreach ($xmlItems as $xmlItem) {
            $ls[] = (string) $xmlItem->link;
        }

        return $ls;
    }

    /**
     * recpere liens api json avec image
     */
    public function getImageByAPI(): array
    {
        $ls2 = array();
        $j = "";
        $i = 0;
        $h = @fopen("https://newsapi.org/v2/top-headlines?country=us&apiKey=c782db1cd730403f88a544b75dc2d7a0", "r");
        while ($b = fgets($h, 4096)) {
            $j .= $b;
        }
        $j = json_decode($j);
        for ($ii = $i + 1; $ii < count($j->articles); $ii++) {
            if ($j->articles[$ii]->urlToImage == "" || empty($j->articles[$ii]->urlToImage) || strlen($j->articles[$ii]->urlToImage) == 0) {
                continue;
            }
            $h = $j->articles[$ii]->url;
            $ls2[$ii] = $h;
        }

        return $ls2;
    }

    public function doublon($t1, $t2)
    {
        foreach ($t1 as $k1 => $v1) {
            $doublon = 0;
            foreach ($t2 as $v2) {
                if ($v2 == $v1) {
                    $doublon = 1;
                }
            }
        }
        return $doublon;
    }

    public function recupereimagedanspage($l)
    {
        if (strstr($l, "commitstrip.com")) {
            $doc = new \DomDocument();
            @$doc->loadHTMLFile($l);
            $xpath = new \DomXpath($doc);
            $xq = $xpath->query('//img[contains(@class,"size-full")]/@src');
            $src = $xq[0]->value;

            return $src;
        } else {
            $doc = new \DomDocument();
            @$doc->loadHTMLFile($l);
            $xpath = new \DomXpath($doc);
            $xq = $xpath->query('//img/@src');
            $src = $xq[0]->value;

            return $src;
        }
    }
}