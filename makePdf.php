<?php
require ('fpdf17/fpdf.php');

class PDF extends FPDF{
    function __construct(){
        parent::FPDF();
    }
    function Header(){
        $this->Cell(60);
        $this->Cell(30,10,'Mjesecni Report',0,0,'C');
        $this->Ln(20);
    } 
    function Footer(){
        $this->SetY(-1);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page'.$this->PageNo().'/{nb}',0,0,'C');
    } 
    
    function BasicTable($header, $data)
    {
        // Header
        foreach($header as $col)
            $this->Cell(40,7,$col,1);
        $this->Ln();
        // Data
        foreach($data as $row)
        {
            foreach($row as $col)
                $this->Cell(40,6,$col,1);
            $this->Ln();
        }
    }
    function FancyTable($header,$data,$total)
    {
        // Colors, line width and bold font
        $this->SetFillColor(255,0,0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('Arial','B');
        // Header
        $w = array(40, 35, 40, 45);
        for($i=0;$i<count($header);$i++)
            $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = false;
        foreach($data as $row)
        {
            $this->Cell($w[0],6,$row[0],'LR',0,'L',$fill);
            $this->Cell($w[1],6,number_format($row[1]),'LR',0,'R',$fill);
            $this->Cell($w[2],6,number_format($row[2]),'LR',0,'R',$fill);
            $this->Cell($w[3],6,$row[3],'LR',0,'L',$fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Closing line
        $this->Cell(array_sum($w),0,'','T');
        $this->Cell(10,10,$total,0,1);
    }
}
function doPdf($data,$total,$firma){
    $pdf = new PDF();
    $header = ['Dan','Sati','Minuta','Cjena'];
    $pdf->SetFont('Arial','',14);
    $pdf->AddPage();
    //$pdf->BasicTable($header,$data,$total);
    $pdf->FancyTable($header,$data,$total);
    $pdf->Output('tmp.pdf','F');
    echo 'tmp.pdf';
}
class makePdf{
    private $pdf = null;
    function __construct(){
        $this->pdf = new PDF();
    }
    function table($data,$total){
        $header = ['Dan','Sati','Minuta','Cjena'];
        $this->pdf->BasicTable($header,$data,$total);
    }
    function generate($data,$total){
        //$this->pdf->AlisasNbPages();
        $this->pdf->AddPage();
        $this->pdf->SetFont('Times','',12);
        foreach($data as $log){
            $this->pdf->Cell(0,10,$log,0,1);
        }
        $this->pdf->Cell(0,80,$total,0,1);
    }
    function output(){
        $this->pdf->Output('tmp.pdf','F');
        echo 'tmp.pdf';
    }

} 

?>

