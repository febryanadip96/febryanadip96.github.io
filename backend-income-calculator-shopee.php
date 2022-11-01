<?php
    error_reporting(E_ALL & ~E_NOTICE);

    $barisAwalPesananDiPenghasilan= $_POST["barisAwalPesananDiPenghasilan"];
    $kolomNoPesananDiPenghasilan= $_POST["kolomNoPesananDiPenghasilan"];
    $kolomPenghasilanDiPenghasilan= $_POST["kolomPenghasilanDiPenghasilan"];

    $barisAwalPesananDiPesanan= $_POST["barisAwalPesananDiPesanan"];
    $kolomNoPesananDiPesanan= $_POST["kolomNoPesananDiPesanan"];
    $kolomNamaBarangDiPesanan= $_POST["kolomNamaBarangDiPesanan"];
    $kolomNamaVarianDiPesanan= $_POST["kolomNamaVarianDiPesanan"];
    $kolomJumlahBarangDiPesanan= $_POST["kolomJumlahBarangDiPesanan"];

    $barisAwalPriceDiPricelist= $_POST["barisAwalPriceDiPricelist"];
    $kolomNamaBarangDiPricelist= $_POST["kolomNamaBarangDiPricelist"];
    $kolomHargaModalDiPricelist= $_POST["kolomHargaModalDiPricelist"];

    $stringPesanan = $_POST["stringPesanan"];
    $stringPenghasilan = $_POST["stringPenghasilan"];
    $stringPricelist = $_POST["stringPricelist"];

    $array_pesanan = explode(PHP_EOL, $stringPesanan);
    $array_penghasilan = explode(PHP_EOL, $stringPenghasilan);
    $array_pricelist = explode(PHP_EOL, $stringPricelist);

    $total_modal = 0;
    $total_penghasilan = 0;
    $untung = 0;
    $total_untung = 0;

    $data_table = [];

    for ( $i = $barisAwalPesananDiPenghasilan; $i < count($array_penghasilan); $i++) {
            $data_penghasilan = explode(',', $array_penghasilan[$i]);

            if(!array_key_exists($kolomNoPesananDiPenghasilan, $data_penghasilan)) break;

            $no_pesanan = $data_penghasilan[$kolomNoPesananDiPenghasilan] ;

            $total_penghasilan = intval($data_penghasilan[$kolomPenghasilanDiPenghasilan]);

            $nama_barang_dan_jumlah = '';

            $text_harga_barang = '';

            $untung = 0;

            $total_modal = 0;

            $pattern = '/\b'.$no_pesanan.'\b/';

            for( $j = $barisAwalPesananDiPesanan; $j < count($array_pesanan); $j++){

                if(intval(preg_match($pattern, $array_pesanan[$j])) > 0){

                    $data_pesanan = explode(',',$array_pesanan[$j]);

                    $nama_barang_dan_jumlah .= $data_pesanan[$kolomNamaBarangDiPesanan].' '. $data_pesanan[$kolomNamaVarianDiPesanan].' <b>' .$data_pesanan[$kolomJumlahBarangDiPesanan].'x</b><br>';

                    $nama_barang_to_check = strtolower($data_pesanan[$kolomNamaBarangDiPesanan]). ' '. strtolower($data_pesanan[$kolomNamaVarianDiPesanan]);

                    $harga_barang = 0;

                    for( $h = $barisAwalPriceDiPricelist; $h < count($array_pricelist); $h++){

                        $data_pricelist = explode(',', $array_pricelist[$h]);

                        $ada = true;

                        if(!array_key_exists($kolomNamaBarangDiPricelist, $data_pricelist)) break;

                        $array_nama_barang_pricelist = explode(' ', strtolower(trim($data_pricelist[$kolomNamaBarangDiPricelist])));

                        for ( $k = 0; $k < count($array_nama_barang_pricelist); $k++) {
                            $pattern2 = '/\b'.trim($array_nama_barang_pricelist[$k]).'\b/';
                            if (intval(preg_match($pattern2, $nama_barang_to_check)) == 0) {
                                $ada = false;
                            }

                        }


                        if($ada){
                            try{
                                $harga = intval($data_pricelist[$kolomHargaModalDiPricelist]);
                            }catch (Exception $e){
                                $harga = 0;
                            }
                        
                            $harga_barang += intval($data_pesanan[$kolomJumlahBarangDiPesanan])*$harga;
                            
                            if(($harga_barang) == '' ) $harga_barang = 0;

                            $text_harga_barang .= $harga_barang . "<br>";

                            $total_modal += $harga_barang;
                            break;
                        }

                        
                    }
                }
            }

            $text_harga_barang .= '(<b>'.$total_modal.'</b>)';
            $untung = $total_penghasilan - $total_modal;
            $total_untung += $untung;

            array_push($data_table, [
                $no_pesanan,
                $nama_barang_dan_jumlah,
                $text_harga_barang,
                $total_penghasilan,
                $untung
            ]);

    }

    
    $object = new stdClass();
    $object->data_table = $data_table;
    $object->total_untung = $total_untung;

    echo json_encode($object);

?>