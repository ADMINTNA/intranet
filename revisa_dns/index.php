<?php
/**
 * Revisión DNS con carga de dominios.txt
 * - Carga/validación .txt con respaldo automático
 * - Procesar archivo existente (mostrando cuántos dominios hay)
 * - Salida: CSV (Excel) o PDF (usa clase externa pdf_dns.php)
 * - Progreso en pantalla y descarga del archivo en /salidas/
 * Compatible con PHP 7.0.33.
 */

date_default_timezone_set('America/Santiago');
ini_set('default_socket_timeout', '5');
set_time_limit(300);

$is_cli = (php_sapi_name() === 'cli');
if ($is_cli) { echo "Este script está pensado para usarse desde el navegador.\n"; exit; }

/* ==========================
   Carga FPDF y clase externa
   ========================== */
define('FPDF_FONTPATH', __DIR__ . '/lib/fpdf/font/');
$pdfDnsAvailable = false;
if (file_exists(__DIR__ . '/lib/fpdf/fpdf.php')) {
    require_once __DIR__ . '/lib/fpdf/fpdf.php';
    if (file_exists(__DIR__ . '/pdf_dns.php')) {
        require_once __DIR__ . '/pdf_dns.php';
        if (class_exists('PDF_DNS')) $pdfDnsAvailable = true;
    }
}

/* ==========================
   Utilidades
   ========================== */
function h($s){ return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function safe_header($name,$value){ if(!headers_sent()) header($name.': '.$value); }
function outln($line){ echo h($line)."\n"; if(function_exists('ob_flush'))@ob_flush(); flush(); }

function ensure_salidas_dir(&$errorOut){
    $salidasDir = __DIR__.DIRECTORY_SEPARATOR.'salidas';
    if(!is_dir($salidasDir)){
        if(!@mkdir($salidasDir,0755,true) && !is_dir($salidasDir)){
            $errorOut[]="No se pudo crear el directorio 'salidas' en ". __DIR__;
            return false;
        }
    }
    return true;
}

function join_or_dash($items){
    $out=array();
    if(is_array($items)){ foreach($items as $it){ $it=trim($it); if($it!=='') $out[]=$it; } }
    return count($out)?implode('; ',$out):'-';
}

function consultar_dns($dominio){
    $R=array('domain'=>$dominio,'ns'=>'-','a'=>'-','aaaa'=>'-','mx'=>'-','cname'=>'-','error'=>'-');

    $ns=@dns_get_record($dominio,DNS_NS); if(is_array($ns)&&count($ns)){ $t=array(); foreach($ns as $r){$t[]=isset($r['target'])?$r['target']:'';} $R['ns']=join_or_dash($t); }
    $a=@dns_get_record($dominio,DNS_A); if(is_array($a)&&count($a)){ $t=array(); foreach($a as $r){$t[]=isset($r['ip'])?$r['ip']:'';} $R['a']=join_or_dash($t); }
    $aaaa=@dns_get_record($dominio,DNS_AAAA); if(is_array($aaaa)&&count($aaaa)){ $t=array(); foreach($aaaa as $r){$t[]=isset($r['ipv6'])?$r['ipv6']:'';} $R['aaaa']=join_or_dash($t); }
    $mx=@dns_get_record($dominio,DNS_MX); if(is_array($mx)&&count($mx)){
        $t=array(); foreach($mx as $r){ $x=isset($r['target'])?$r['target']:''; if(isset($r['pri'])) $x.=' (prio '.$r['pri'].')'; $t[]=$x; }
        $R['mx']=join_or_dash($t);
    }
    $cname=@dns_get_record($dominio,DNS_CNAME); if(is_array($cname)&&count($cname)){ $t=array(); foreach($cname as $r){$t[]=isset($r['target'])?$r['target']:'';} $R['cname']=join_or_dash($t); }

    if($R['ns']==='-'&&$R['a']==='-'&&$R['aaaa']==='-'&&$R['mx']==='-'&&$R['cname']==='-'){ $R['error']='Sin registros o tiempo de espera'; }
    return $R;
}

/* CSV con separador ";" para Excel */
function escribir_csv($filas,$ruta){
    $fp=@fopen($ruta,'w'); if(!$fp) return false;
    fwrite($fp,"\xEF\xBB\xBF"); // BOM UTF-8 (Excel-friendly)
    fputcsv($fp,array('Dominio','NS','A','AAAA','MX','CNAME','Error'), ';');
    foreach($filas as $f){
        fputcsv($fp,array(
            $f['domain'],$f['ns'],$f['a'],$f['aaaa'],$f['mx'],$f['cname'],$f['error']
        ), ';');
    }
    fclose($fp);
    return true;
}

/* Generar PDF usando clase externa PDF_DNS */
function generar_pdf($filas,$ruta,&$err){
    if (!class_exists('PDF_DNS')) {
        $err = "No se encontró la clase PDF_DNS / FPDF. Verifica pdf_dns.php y lib/fpdf/fpdf.php + font/.";
        return false;
    }

    $pdf = new PDF_DNS('L','mm','A4');
    $pdf->generatedAt = date('Y-m-d H:i:s'); // misma fecha/hora en todas las páginas
    $pdf->SetMargins(10, 25, 10);            // margen superior extra por encabezado
    $pdf->SetAutoPageBreak(true, 18);        // margen inferior para pie
    $pdf->AliasNbPages();

    // Config de tabla para formato Carta (Letter) apaisado
    // Ancho útil ≈ 259 mm
    $w = array(48,38,28,28,60,37,20); 
    $headers = array('Dominio','NS','A','AAAA','MX','CNAME','Error');
    $pdf->SetWidths($w);
    $pdf->SetAligns(array('L','L','L','L','L','L','L'));
    $pdf->SetTableHeader($headers);    
    
    $pdf->AddPage();
    $pdf->SetFont('Arial','',8);

    // Filas
    foreach($filas as $row){
        $vals = array(
            $row['domain'],
            $row['ns'],
            $row['a'],
            $row['aaaa'],
            $row['mx'],
            $row['cname'],
            $row['error']
        );
        $pdf->Row($vals);
    }

    $pdf->Output('F', $ruta);
    return true;
}

function contar_dominios_archivo($path){
    if (!file_exists($path)) return 0;
    $count = 0;
    foreach (file($path) as $linea) {
        $linea = trim($linea);
        if ($linea === '' || strpos($linea, '#') === 0) continue;
        $count++;
    }
    return $count;
}

/* ==========================
   HTML HEADERS
   ========================== */
@ini_set('output_buffering','off');
@ini_set('zlib.output_compression','0');
safe_header('Content-Type','text/html; charset=UTF-8');
safe_header('Cache-Control','no-cache, no-store, must-revalidate');
safe_header('Pragma','no-cache');
safe_header('Expires','0');
safe_header('X-Accel-Buffering','no');
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Revisión DNS de dominios .cl</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
:root{--bg:#0b0f14;--fg:#e6edf3;--muted:#9aa4af;--accent:#8ab4f8;--ok:#7ee787;--err:#ff7b72;--warn:#ffa657;}
body{margin:0;background:var(--bg);color:var(--fg);font:14px/1.5 ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial;}
.container{max-width:960px;margin:32px auto;padding:0 16px;}
h1{margin:0 0 16px 0;font-size:22px}
.card{background:#0f141a;border:1px solid #1f2937;border-radius:12px;padding:16px;margin:16px 0;box-shadow:0 1px 10px rgba(0,0,0,.2)}
label{display:block;margin-bottom:8px}
input[type="file"]{display:block;margin-top:8px}
button,.btn{background:#1f6feb;color:#fff;border:none;padding:10px 14px;border-radius:8px;cursor:pointer}
.btn-secondary{background:#2d333b}
.btn:disabled{opacity:.6;cursor:not-allowed}
a{color:var(--accent)}
small,.muted{color:var(--muted)}
pre.log{white-space:pre-wrap;background:#0b0f14;border:1px solid #1f2937;border-radius:8px;padding:12px;max-height:60vh;overflow:auto}
.status-ok{color:var(--ok)} .status-err{color:var(--err)} .status-warn{color:var(--warn)}
.code{background:#0b0f14;border:1px solid #1f2937;padding:2px 6px;border-radius:6px}
.radio-row{display:flex;gap:16px;align-items:center;margin:8px 0}
</style>
</head>
<body>
<div class="container">
  <h1>Revisión DNS de dominios .cl</h1>

  <!-- Instrucciones de formato -->
  <div class="card">
    <h3 style="margin-top:0">Instrucciones de formato del archivo <span class="code">.txt</span></h3>
    <ul>
      <li>Un <strong>dominio por línea</strong>. Ej.: <span class="code">ejemplo.cl</span></li>
      <li>Se <strong>ignoran</strong> líneas vacías y líneas que <strong>comienzan con</strong> <span class="code">#</span> (comentarios).</li>
      <li>Caracteres permitidos: letras <span class="code">a-z</span>, números <span class="code">0-9</span>, guion <span class="code">-</span> y puntos <span class="code">.</span></li>
      <li>Se <strong>eliminan duplicados</strong> automáticamente.</li>
      <li>Validación de formato de dominio (ej.: <span class="code">sub.ejemplo.cl</span>). <em>Opcional:</em> puedo restringir estrictamente a <span class="code">.cl</span> si lo deseas.</li>
      <li>Tamaño máximo del archivo: <strong>2&nbsp;MB</strong>.</li>
    </ul>
    <div style="border:1px dashed #374151;border-radius:8px;padding:10px;margin-top:8px">
<pre class="log"># Lista de dominios a revisar
ejemplo.cl
sub.ejemplo.cl

# Comentarios y líneas vacías son ignorados
otrodominio.cl
</pre>
    </div>
  </div>

<?php
/* ==========================
   Manejo de carga de archivo
   ========================== */
$messages = array();
$errors   = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'subir') {
    if (!isset($_FILES['archivo']) || !is_array($_FILES['archivo'])) {
        $errors[] = "No se recibió ningún archivo.";
    } else {
        $f = $_FILES['archivo'];
        if ($f['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Error al subir el archivo (código {$f['error']}).";
        } else {
            $name = $f['name']; $size=(int)$f['size']; $tmp=$f['tmp_name'];
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if ($ext !== 'txt') { $errors[] = "El archivo debe ser .txt"; }
            if ($size <= 0 || $size > 2*1024*1024) { $errors[] = "El archivo debe pesar entre 1 byte y 2 MB."; }
            $raw = '';
            if (!count($errors)) { $raw=@file_get_contents($tmp); if($raw===false) $errors[]="No se pudo leer el archivo subido."; }

            $dominios = array(); $lineErrors=array();
            if (!count($errors)) {
                $lines = preg_split('/\r\n|\r|\n/',$raw);
                $lineNum=0; $seen=array();
                foreach($lines as $ln){
                    $lineNum++;
                    $line=trim($ln);
                    if($line==='' || strpos($line,'#')===0) continue;
                    $line=strtolower($line);
                    if(!preg_match('/^(?:[a-z0-9-]+\.)+[a-z]{2,}$/',$line)){
                        $lineErrors[]="Línea {$lineNum}: dominio inválido \"{$line}\"";
                        continue;
                    }
                    // Para obligar .cl, descomentar:
                    // if (!preg_match('/\.cl$/',$line)){ $lineErrors[]="Línea {$lineNum}: solo .cl"; continue; }
                    if(isset($seen[$line])) continue;
                    $seen[$line]=true;
                    $dominios[]=$line;
                }
                if(!count($dominios)) $errors[]="El archivo no contiene dominios válidos.";
                if(count($lineErrors)){
                    $messages[]="<span class=\"status-warn\">Advertencias de formato:</span><br>". h(implode("\n",$lineErrors));
                }
            }

             if (!count($errors) && count($dominios)) {
                // Ordenar alfabéticamente antes de guardar
                sort($dominios, SORT_NATURAL | SORT_FLAG_CASE);

                $baseDir = __DIR__; 
                $dest=$baseDir.DIRECTORY_SEPARATOR.'dominios.txt';

                if (file_exists($dest)) {
                    $backup=$baseDir.DIRECTORY_SEPARATOR.'dominios_'.date('Ymd_His').'.txt';
                    if(!@rename($dest,$backup)){ 
                        $errors[]="No se pudo respaldar el dominios.txt existente."; 
                    } else { 
                        $messages[]="Respaldo creado: ".h(basename($backup)); 
                    }
                }

                if(!count($errors)){
                    $data="# generado ".date('Y-m-d H:i:s')."\n".implode("\n",$dominios)."\n";
                    if(@file_put_contents($dest,$data)===false){ 
                        $errors[]="No se pudo crear el nuevo dominios.txt (revisa permisos)."; 
                    } else { 
                        $messages[]="<span class=\"status-ok\">Se ha subido archivo correcto</span>: ".h(basename($name))." → dominios.txt (".count($dominios)." dominios)"; 
                    }
                }
            }
                    
        }
    }
}
?>

  <div class="card">
    <form method="post" enctype="multipart/form-data" onsubmit="document.getElementById('btnSubir').disabled=true;">
      <input type="hidden" name="accion" value="subir">
      <label><strong>¿Deseas subir un archivo .txt con dominios?</strong></label>
      <input type="file" name="archivo" accept=".txt" required>
      <small class="muted">Se validará el formato y se eliminarán duplicados. El archivo actual (si existe) se respaldará automáticamente.</small>
      <div style="margin-top:12px;">
        <button id="btnSubir" type="submit" class="btn">Subir archivo</button>
        <a class="btn btn-secondary" href="?">Cancelar</a>
      </div>
    </form>
  </div>

<?php if (count($messages)): ?>
  <div class="card">
    <h3 style="margin-top:0">Resultado de la carga</h3>
    <div><?php echo implode("<br>", $messages); ?></div>
  </div>
<?php endif; ?>

<?php if (count($errors)): ?>
  <div class="card" style="border-color:#3b1216">
    <h3 style="margin-top:0" class="status-err">Errores</h3>
    <ul><?php foreach ($errors as $e): ?><li><?php echo h($e); ?></li><?php endforeach; ?></ul>
    <p>Corrige los problemas y vuelve a subir el archivo.</p>
  </div>
<?php endif; ?>

<?php
  // Detectar archivo existente y contar dominios
  $domFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'dominios.txt';
  $domCount = contar_dominios_archivo($domFilePath);
  $hayArchivo = file_exists($domFilePath);
?>
  <div class="card">
    <h3 style="margin-top:0">Paso 2: Ejecutar revisión DNS</h3>

    <?php if ($hayArchivo && $domCount > 0): ?>
      <p class="muted">
        Se detectó <code>dominios.txt</code> con <strong><?php echo $domCount; ?></strong> dominio(s).
        Puedes procesarlo ahora sin subir un archivo nuevo.
      </p>
      <form method="post" onsubmit="document.getElementById('btnRevisar').disabled=true;">
        <input type="hidden" name="accion" value="procesar">
        <div class="radio-row">
          <label><input type="radio" name="fmt" value="csv" checked> CSV (Excel)</label>
          <label><input type="radio" name="fmt" value="pdf" <?php echo $pdfDnsAvailable?'':'disabled'; ?>> PDF</label>
        </div>
        <small class="muted">
          <?php if ($pdfDnsAvailable): ?>
            PDF disponible (usa <code>pdf_dns.php</code> + <code>lib/fpdf/fpdf.php</code>).
          <?php else: ?>
            Para PDF, sube <code>pdf_dns.php</code> y <code>lib/fpdf/fpdf.php</code> (con carpeta <code>font/</code>).
          <?php endif; ?>
        </small>
        <div style="margin-top:12px;">
          <button id="btnRevisar" class="btn" type="submit">
            Iniciar revisión (<?php echo $domCount; ?> dominio<?php echo $domCount===1?'':'s'; ?>)
          </button>
        </div>
      </form>
    <?php else: ?>
      <p class="muted">
        No se encontró un <code>dominios.txt</code> válido. Sube un archivo en el Paso 1
        (o verifica que no esté vacío / solo comentarios).
      </p>
      <button class="btn" type="button" disabled>Iniciar revisión</button>
    <?php endif; ?>
  </div>

<?php
/* ==========================
   PROCESAR (revisión DNS)
   ========================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'procesar') {
    echo '<div class="card"><h3 style="margin-top:0">Progreso</h3><pre class="log">';
    echo str_repeat(" ", 1024) . "\n"; if(function_exists('ob_flush'))@ob_flush(); flush();

    $fmt = isset($_POST['fmt']) ? strtolower(trim($_POST['fmt'])) : 'csv';
    if($fmt!=='csv' && $fmt!=='pdf'){ $fmt='csv'; }

    $domFile = __DIR__ . DIRECTORY_SEPARATOR . 'dominios.txt';
    if (!file_exists($domFile)) {
        outln("ERROR: No se encuentra dominios.txt. Primero sube un archivo válido.");
        echo '</pre></div>';
    } else {
        $prepOkErrors=array();
        $prepOk = ensure_salidas_dir($prepOkErrors);
        if (!$prepOk) { foreach($prepOkErrors as $e) outln("ERROR: ".$e); echo '</pre></div>'; }
        else {
            $dominios = array();
            foreach (file($domFile) as $linea) {
                $linea = trim($linea);
                if ($linea === '' || strpos($linea, '#') === 0) continue;
                $dominios[] = strtolower($linea);
            }
            if (!count($dominios)) { outln("ERROR: dominios.txt está vacío o solo tiene comentarios."); echo '</pre></div>'; }
            else {
                $total=count($dominios); outln("Total dominios: ".$total."\n");
                $filas=array(); $i=0;
                foreach($dominios as $d){
                    $i++; outln("[$i/$total] ".$d." -> consultando...");
                    $res=consultar_dns($d); $filas[]=$res;
                    $tipos=array(); if($res['ns']!=='-')$tipos[]='NS'; if($res['a']!=='-')$tipos[]='A'; if($res['aaaa']!=='-')$tipos[]='AAAA'; if($res['mx']!=='-')$tipos[]='MX'; if($res['cname']!=='-')$tipos[]='CNAME';
                    $txt=count($tipos)?implode(',',$tipos):'sin registros'; outln("    ok (".$txt.")\n");
                }

                $salidasDir=__DIR__.DIRECTORY_SEPARATOR.'salidas';
                $nombreBase='dns_resultados_'.date('Ymd_Hi');

                if($fmt==='csv'){
                    $salidaNombre=$nombreBase.'.csv';
                    $salidaRuta=$salidasDir.DIRECTORY_SEPARATOR.$salidaNombre;
                    $ok=escribir_csv($filas,$salidaRuta);
                    if($ok){
                        outln("Archivo CSV generado: salidas/".$salidaNombre);
                        $baseUrl=(isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']==='on'?'https':'http').'://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['SCRIPT_NAME']),'/\\').'/';
                        $url=$baseUrl.'salidas/'.$salidaNombre;
                        echo "</pre><p>Descargar CSV: <a href=\"",h($url),"\" download>",h($salidaNombre),"</a></p>";
                        echo "<p class=\"muted\">Ruta en servidor: <code>",h($salidaRuta),"</code></p></div>";
                    } else {
                        outln("ERROR: No se pudo escribir el CSV en: ".$salidaRuta);
                        echo '</pre></div>';
                    }
                } else { // PDF
                    if (!$pdfDnsAvailable) {
                        outln("ERROR: PDF no disponible. Sube pdf_dns.php y lib/fpdf/fpdf.php (con carpeta font/).");
                        echo '</pre></div>';
                    } else {
                        $salidaNombre=$nombreBase.'.pdf';
                        $salidaRuta=$salidasDir.DIRECTORY_SEPARATOR.$salidaNombre;
                        $errPdf='';
                        $ok=generar_pdf($filas,$salidaRuta,$errPdf);
                        if($ok){
                            outln("Archivo PDF generado: salidas/".$salidaNombre);
                            $baseUrl=(isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']==='on'?'https':'http').'://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['SCRIPT_NAME']),'/\\').'/';
                            $url=$baseUrl.'salidas/'.$salidaNombre;
                            echo "</pre><p>Descargar PDF: <a href=\"",h($url),"\" download>",h($salidaNombre),"</a></p>";
                            echo "<p class=\"muted\">Ruta en servidor: <code>",h($salidaRuta),"</code></p></div>";
                        } else {
                            outln("ERROR: No se pudo generar el PDF. ".$errPdf);
                            echo '</pre></div>';
                        }
                    }
                }
            }
        }
    }
}
?>

  <div class="card">
    <h3 style="margin-top:0">Ayuda rápida</h3>
    <ul>
      <li>Formato permitido: <code>.txt</code>, hasta 2&nbsp;MB.</li>
      <li>Un dominio por línea; líneas en blanco o con <code>#</code> se ignoran.</li>
      <li>Se respaldará el <code>dominios.txt</code> existente como <code>dominios_YYYYmmdd_HHMMSS.txt</code>.</li>
      <li>El archivo de salida se guardará en <code>/salidas/</code> con nombre <code>dns_resultados_YYYYMMDD_HHMM</code> (CSV o PDF).</li>
      <li>Para <strong>PDF</strong> necesitas <code>pdf_dns.php</code> y <code>lib/fpdf/fpdf.php</code> (con carpeta <code>font/</code>). Logo opcional: <code>logo.png</code> o <code>logo.jpg</code>.</li>
    </ul>
  </div>

</div>
</body>
</html>