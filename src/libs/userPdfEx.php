<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class UserPdfEx extends FPDF {

    private $date;
    private $serie;
    private $proprio;
    private $conf;
    private $posy;
    private $members = array();
    private $header = '';
    private $border = 0;
    private $curpage = 1;
    private $maxpages = 0;

    public function init($serie, $proprio) {
        $this->date = strftime('%e %B %G, %H:%M:%S');
        $this->serie = $serie;
        $this->proprio = $proprio;
        $this->reload_conf(0);
    }

    public function Header() {
        $this->border = 0;
        $this->addptext('vdr_head', 'Mandat : ', 10, 15);
        $this->addptext('vdr_details', $this->serie, 50, 15);
        $this->addptext('vdr_head', 'Utilisateur : ', 10, 20);
        $this->addptext('vdr_details', $this->proprio, 50, 20);
        $this->addptext('vdr_head', 'Date : ', 110, 15);
        $this->addptext('vdr_details', $this->date, 150, 15);
        $this->addptext('vdr_head', 'Page : ', 110, 20);
        $this->addptext('vdr_details', $this->curpage++ . '/' . $this->maxpages, 150, 20);
        $this->Line(10, 25, 200, 25);

        switch ($this->header) {
            case 'table':
                $this->border = 1;
                $this->addptext('vdr_head', 'Numero', 10, 30, 20, 7);
                $this->addptext('vdr_head', 'ID', 30, 30, 30, 7);
                $this->addptext('vdr_head', 'Nom', 60, 30, 50, 7);
                $this->addptext('vdr_head', 'Prenom', 110, 30, 50, 7);
                $this->addptext('vdr_head', 'Emargement', 160, 30, 40, 7);
                $this->posy = 37;
                break;
            default:
                $this->posy = 30;
        }
    }

    private function reload_conf($type) {
        require dirname(__FILE__) . '/ticketgen/' . 'config.php';
        $this->conf = $config['default'];
        if (isset($config[$type]))
            $this->conf = array_merge_recursive($config['default'], $config[$type]);
    }

    private function colordecode($color, &$result) {
        $result = array(
            intval(substr($color, 1, 2)),
            intval(substr($color, 3, 2)),
            intval(substr($color, 5, 2)),
        );
    }

    private function addptext($styleconf, $text, $posx = null, $posy = null, $width = 0, $height = 0) {
        static $cfonts = array();

        $font = $this->conf[$styleconf . '_font'];
        $size = isset($this->conf[$styleconf . '_size']) ? $this->conf[$styleconf . '_size'] : 10;
        $style = isset($this->conf[$styleconf . '_style']) ? $this->conf[$styleconf . '_style'] : '';
        $color = isset($this->conf[$styleconf . '_color']) ? $this->conf[$styleconf . '_color'] : '#000000';
        $height = isset($this->conf[$styleconf . '_height']) ? $this->conf[$styleconf . '_height'] : $height;
        $text = utf8_decode($text);

        if (!is_file(FPDF_FONTPATH . $font . '.php')) {
            ob_start();
            MakeFont(FPDF_FONTPATH . $font . '.ttf');
            file_put_contents(FPDF_FONTPATH . $font . '.log', ob_get_contents());
            ob_end_clean();
        }

        if (!isset($cfonts[$font]))
            $cfonts[$font] = $this->AddFont($font, $style, $font . '.php');
        $this->SetFont($font, $style, $size);
        $this->colordecode($color, $comp);

        $this->SetTextColor($comp[0], $comp[1], $comp[2]);

        if ($posx !== null && $posy !== null) {
            $this->SetXY($posx, $posy);
            $this->Cell($width, $height, $text, $this->border);
        } else
            $this->Write($height, $text);
    }

    public function __construct($serie, $proprio, $members) {
        parent::__construct();
        $this->init($serie, $proprio);
        $this->members = $members;
        $this->maxpages = ceil(count($members) / 38);
        $this->SetCreator('EPITANIME', true);
        $this->SetAuthor('EPITANIME - Intranet', true);
        $this->SetTitle('EPITANIME - Membres', true);
    }

    public function out($out = 'I') {
        $this->Output('Serie.pdf', $out);
    }

    public function mktable() {
        $this->header = 'table';
        $this->border = 1;
        $this->AddPage();
        $number = 1;
        foreach ($this->members as $member) {
            if ($this->posy + 7 > 297)
                $this->AddPage();

            $this->addptext('vdr_details', $number, 10, $this->posy, 20, 7);
            $this->addptext('vdr_details', $member['user_id'], 30, $this->posy, 30, 7);
            $this->addptext('vdr_details', $member['user_lastname'], 60, $this->posy, 50, 7);
            $this->addptext('vdr_details', $member['user_firstname'], 110, $this->posy, 50, 7);
            $this->addptext('vdr_details', '', 160, $this->posy, 40, 7);

            $this->posy += 7;
            $number++;
        }
    }

}
