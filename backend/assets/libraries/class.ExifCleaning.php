<?php 
	class ExifCleaning {
		private static function reflejarImagen ($imagenOriginal) {
			$anchura = imagesx ($imagenOriginal);
			$altura = imagesy ($imagenOriginal);

			$origenDeX = $anchura -1;
			$origenDeY = 0;
			$anchura_original = -$anchura;
			$altura_original = $height;

			$imagenDeDestino = imagecreatetruecolor ($anchura, $altura);

			if (imagecopyresampled ($imagenDeDestino, $imagenOriginal, 0, 0, $origenDeX, $origenDeY, $anchura, $altura, $anchura_original, $altura_original)) return $imagenDeDestino;

			return $imagenOriginal;
		}
	 
		public static function adjustImageOrientation($ficheroDeImagen) {			 
			$codificacionExif = exif_read_data($ficheroDeImagen);
			if($codificacionExif && isset($codificacionExif['Orientation'])) {
				$orientacion = $codificacionExif['Orientation'];
				if($orientacion != 1){
					$imagenEnProceso = imagecreatefromjpeg($ficheroDeImagen);
					$reflejo = false;
					$grados = 0;
					switch ($orientacion) {
						case 2:
							$reflejo = true;
							break;
						case 3:
							$grados = 180;
							break;
						case 4:
							$grados = 180;
							$reflejo = true; 
							break;
						case 5:
							$grados = 270;
							$reflejo = true; 
							break;
						case 6:
							$grados = 270;
							break;
						case 7:
							$grados = 90;
							$reflejo = true; 
							break;
						case 8:
							$grados = 90;
							break;
					}
					if ($grados) $imagenEnProceso = imagerotate($imagenEnProceso, $grados, 0); 
					if ($reflejo) $imagenEnProceso = self::reflejarImagen($imagenEnProceso);
					imagejpeg($imagenEnProceso, $ficheroDeImagen);
				}
			}
			return true;
		}
	}
?>
