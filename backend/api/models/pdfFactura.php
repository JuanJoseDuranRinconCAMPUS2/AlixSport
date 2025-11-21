<?php

    class PdfFacturaModel {

        public static function generarHTML($usuario, $productos, $total) {
            
            date_default_timezone_set("America/Bogota");
            $fecha = date("d/m/Y");
            $hora = date("h:i A");

            $logoPath = realpath(__DIR__ . '/../../../frontend/src/assets/axil par.png');
            $logoBase64 = base64_encode(file_get_contents($logoPath));

            $html = "
            <style>
                body { font-family: 'Arial', sans-serif; background: #111; color: #e0e0e0; padding: 25px; }
                .factura-container { background: #1b1b1b; border-radius: 12px; padding: 25px; border: 1px solid #00ff7f33; }
                .encabezado { border-bottom: 2px solid #00ff7f; padding-bottom: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; }
                .titulo { font-size: 26px; font-weight: bold; color: #00ff7f; }
                .info-empresa { font-size: 14px; line-height: 18px; text-align: right; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th { background: #00ff7f; color: #000; padding: 12px; font-size: 15px; text-align: left; }
                td { padding: 12px; border-bottom: 1px solid #333; }
                tr:hover { background: #0b2b0b; transition: 0.3s ease-in-out; }
                .resumen-total { font-size: 22px; font-weight: bold; text-align: right; margin-top: 25px; color: #00ff7f; }
                .info-cliente { margin-top: 5px; font-size: 16px; color: #cfcfcf; }
                .logo { width: 180px; margin-bottom: 10px; filter: drop-shadow(0 0 8px #00ff7f); }
                .footer { margin-top: 35px; text-align: center; font-size: 13px; color: #8f8f8f; }
            </style>

            <div class='factura-container'>
                <div class='encabezado'>
                    <div>
                        <img src='data:image/png;base64,{$logoBase64}' class='logo' />
                        <div class='info-cliente'>Cliente: <strong>{$usuario}</strong></div>
                    </div>
                    
                    <div class='info-empresa'>
                        <strong>FACTURA ELECTRÓNICA</strong><br>
                        Fecha: {$fecha}<br>
                        Hora: {$hora}<br>
                        NIT: 156.5655.65645<br>
                        Email: alixsport.suplementos.contacto@gmail.com<br>
                        Tel: +57 300 4803017
                    </div>
                </div>

                <table>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                    </tr>";

                    foreach ($productos as $p) {
                        $html .= "
                        <tr>
                            <td>{$p['nombre_Producto']}</td>
                            <td>{$p['cantidad']}</td>
                            <td>$ " . number_format($p['precio_unitario'], 0, ',', '.') . "</td>
                            <td>$ " . number_format($p['subtotal'], 0, ',', '.') . "</td>
                        </tr>";
                    }

            $html .= "
                </table>

                <div class='resumen-total'>TOTAL A PAGAR: $ " . number_format($total, 0, ',', '.') . "</div>

                <div class='footer'>
                    Gracias por tu compra!  
                    <br>AlixSport — Suplementos de alto rendimiento para atletas
                </div>
            </div>";

            return $html;
        }
    }
?>