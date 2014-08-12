<?php

class Service_Prescription extends Service_Abstract
{
    public function find($article, $texte)
    {
        libxml_use_internal_errors(true);

        $documents = array();
        $page = 1;

        do {
            $client = new Zend_Http_Client;
            $client->setUri('http://www.legifrance.gouv.fr/rechExpTexteCode.do');
            $client->setParameterGet('reprise', 'true');
            $client->setParameterGet('page', $page);
            $client->setParameterPost('champNumeroArticle', $article);
            $client->setParameterPost('rechFT1', $texte);
            $response = $client->request('POST');
            $doc = new DOMDocument();
            $doc->loadHTML($response->getBody());
            $xpath = new Domxpath($doc);
            $nodes = $xpath->query("//a[contains(@href,'affichCodeArticle.do') or contains(@href,'affichTexteArticle.do')]");
            foreach ($nodes as $node) {
                $url = 'http://www.legifrance.gouv.fr' . substr($node->getAttribute('href'), 2);
                $url = preg_replace('/'.preg_quote(strstr( substr( $url, strpos( $url, '.do') + strlen( '.do')), '?', true)).'/s', '', $url);
                parse_str(parse_url($url)['query'], $url_params);
                $documents[] = array(
                    'value' => $node->getAttribute('title') . ': ' . trim(preg_replace('/\s+/', ' ', $node->nodeValue)),
                    'href' => $url,
                    'id_article' => $url_params['idArticle'],
                    'id_texte' => $url_params['cidTexte']
                );
            }
            $page++;
        } while($nodes->length == 20);

        return $documents;
    }

    public function get($id_article, $id_texte)
    {
        libxml_use_internal_errors(true);

        $document = array();

        $client = new Zend_Http_Client;
        $client->setUri('http://www.legifrance.gouv.fr/affichCodeArticle.do');
        $client->setParameterGet('idArticle', $id_article);
        $client->setParameterGet('cidTexte', $id_texte);
        $response = $client->request('GET');
        $doc = new DOMDocument();
        $doc->loadHTML($response->getBody());
        $xpath = new Domxpath($doc);
        $document['article'] = $xpath->query("//div[@class='liensArtCita']/*")->item(0)->nodeValue;
        $document['title'] = $xpath->query("//div[@class='titreArt']")->item(0)->nodeValue;
        $document['content'] = $xpath->query("//div[@class='corpsArt']")->item(0)->nodeValue;

        return $document;
    }
}
