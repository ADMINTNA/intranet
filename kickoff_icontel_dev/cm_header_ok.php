<?php
// ==========================================================
// kickoff/cm_header.php
// Header de KickOff
// Autor: Mauricio Araneda
// Fecha: 2025-11-17
// Codificación: UTF-8 sin BOM
// ==========================================================
mb_internal_encoding("UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
?>
<meta charset="UTF-8">

<style type="text/css">
body,td,th { font-family: Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif; }
a { color: #000000; }

/* Corrige separación extra */
.no-sort, .no-sort td, .no-sort tr {
    padding: 0 !important;
    margin: 0 !important;
    border-spacing: 0 !important;
    line-height: 0 !important;
}

/* === Frases motivacionales === */
#frase-centro {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  font-weight: 600;
  color: #512554;
  width: 100%;
  height: 100%;
  text-align: center;
}

#frase-motivacional {
  margin: 0;
  text-align: right;
  font-size: 12px;
  font-weight: 600;
  color: #512554;
}

@media (min-width: 768px){
 #frase-centro {font-size: 16px;}
 #frase-motivacional{ font-size: 18px; }
}
</style>

<!-- ========================================================== -->
<!-- 🧭 FUNCIÓN GLOBAL DE SALIDA FUERA DEL IFRAME -->
<!-- ========================================================== -->
<script>
function salirDelSistema() {
    if (!confirm("¿Deseas cerrar tu sesión y salir del sistema?")) return false;

    window.top.location.href = "https://intranet.icontel.cl/index.php";
    return false; 
}
</script>

<table class="no-sort">
  <tbody>
    <tr>
      <!-- 🔥 CORREGIDO: se eliminaron alturas invisibles -->
      <td valign="top" style="padding:0;margin:0;height:0;line-height:0;border:none;">
        <table class="no-sort" border="0">
          <tbody>

            <!-- 🧠 Fila 1 -->
            <tr style="color:white;background-color:#64C2C8;">
              <td width="6%" rowspan="2" align="center" valign="bottom">

                <a href="#"
                   title="Cerrar sesión y salir del sistema"
                   onclick="return salirDelSistema();">
                   <img src="../kickoff/images/Robot_Cool_01.png"
                        width="80"
                        alt="Cerrar sesión"
                        style="height:112px; cursor:pointer;">
                </a>

              </td>

              <td width="15%" rowspan="2" align="center" valign="middle">
                <em>"Cuando todos vendemos, el Éxito es inevitable"</em>
              </td>

              <td style="color:#512554;height:30px;vertical-align:middle;" colspan="7">
                <div style="display:flex;justify-content:space-between;align-items:center;padding-top:6px;">
                  <div id="frase-centro"></div>
                  <div id="frase-motivacional"></div>
                </div>
              </td>
            </tr>

            <!-- 🧩 Fila 2 -->
            <tr style="color:white;background-color:#64C2C8;">

              <td>
                <select style="color:white;font-weight:bold;text-decoration:underline;background-color:#64C2C8;border:none;"
                        name="calculadoras" id="calculadoras" onchange="manejarCambio(this)">
                  <option value="">Calculadoras</option>
                  <option value="https://intranet.icontel.cl/fotovoltaico/">Fotovoltaica</option>
                </select>
              </td>

              <td>
                <select style="color:white;font-weight:bold;text-decoration:underline;background-color:#64C2C8;border:none;"
                        name="formularios" id="formulario" onchange="manejarCambio(this)">
                  <option value="">Formularios</option>
                  <option value="https://sweet.icontel.cl/custom/New_lead/new_lead.html">Referidos</option>
                  <option value="https://forms.gle/KckuqAywwEk755KU9">Fotovoltaico</option>
                </select>
              </td>

              <td>
                <select style="color:white;font-weight:bold;text-decoration:underline;background-color:#64C2C8;border:none;"
                        name="materiales" id="materiales" onchange="manejarCambio(this)">
                  <option value="">Marketing y Escalamiento</option>
                  <option value="https://drive.google.com/drive/folders/1caqwj2gJSbA0VYAg9MVWlJz5SogPjM2i?usp=sharing">Material/Marketing</option>
                  <option value="/images/organigrama.jpg">Organigrama TNA</option>
                  <option value="https://drive.google.com/drive/folders/1AWTF-sgYBQiKrQrHN8SdVgTwdyr5E_LU?usp=sharing">Escalamiento Proveedor</option>
                </select>
              </td>

              <td align="center">
                <a style="color:white;font-weight:bold;" href="https://calendar.app.google/9oeDbrLGtM3Gzhn28" target="_blank">
                  Agendamiento Comercial
                </a>
              </td>

              <td align="center">
                <a style="color:white;font-weight:bold;" href="https://www.tnagroup.cl/telecomunicaciones/presentacion" target="_blank">
                  Presentación Empresa
                </a>
              </td>

              <td align="center" style="white-space:nowrap;">
                <div id="auto-refresh-bar">
                  <form id="autoRefreshForm" method="post" style="margin:0;display:inline;">
                    <label style="cursor:pointer;">
                      🔁 Actualizar c/5 min.
                      <input type="checkbox" name="auto_refresh" id="autoRefreshToggle"
                             value="true" <?php echo $autoRefreshState ? 'checked' : ''; ?>
                             style="vertical-align:middle;margin-left:6px;">
                    </label>
                  </form>
                </div>
              </td>

              <td align="right">
                <!--button onclick="mostrarTodasLasCapas()">Mostrar iTems</button-->
              </td>
            </tr>

            <!-- ⚙️ Fila 3 -->
            <tr style="background-color:#1F1D3E;color:white;">
              <td colspan="2" height="38">
                <p class="infoheader2">UF <?php echo $UF; ?>&nbsp;&nbsp;US <?php echo $USD; ?>&nbsp;&nbsp;Al <?php echo $UF_Fecha; ?></p>
              </td>

              <td align="center"><p class="botonheader2"><text onclick="mostrarSolo('capa_casos')" onMouseOver="this.style.cursor='pointer'"/><b>Casos</b></text></p></td>

              <td align="center"><p class="botonheader2"><text onclick="mostrarSolo('capa_iconos')" onMouseOver="this.style.cursor='pointer'"/><b>Favoritos</b></text></p></td>

              <td align="center"><p class="botonheader2"><text onclick="mostrarSolo('capa_buscadores')" onMouseOver="this.style.cursor='pointer'"/><b>Buscadores</b></text></p></td>

              <td></td>

              <td align="center"><p class="infoheader2" id="reloj">Hora: --:--:--</p></td>

              <td align="center"><p class="infoheader2">Cuadro de Mando de <?php echo $sg_name; ?></p></td>

              <td width="11%" align="right">
                <form action="" method="post" name="form_select" id="form_select">
                  <p><?php echo $select; ?></p>
                </form>
              </td>
            </tr>

          </tbody>
        </table>
      </td>
    </tr>
  </tbody>
</table>

<!-- FRASES MOTIVACIONALES -->
<script>
// ... (tu código original sin cambios)
</script>

<?php
// AUTOREFRESH
// ... (tu código original sin cambios)
?>

<!-- ========================================================== -->
<!-- 🎯 FRASES MOTIVACIONALES -->
<!-- ========================================================== -->
<script>
const frases = [
  "#todossomosCHUCU-CHUCU!", "#PilasQueArrancamos", "#ConTodoYSinParar", "#TodosEnLaMismaVibra",
  "#SubidosAlMismoBus", "#SomosUnTrenImparable", "#EstoEsConGanas", "#PaDelanteConFuerza",
  "#UnSoloRitmoUnSoloEquipo", "#ConEnergíaAvanzamos", "#TodosALaMismaMarcha", "#PaLanteQueSiSePuede",
  "#SomosElMismoBeat", "#JuntosEnElMismoTren", "#AFullConTodo", "#LoDamosTodoSiempre",
  "#DaleQueDaleConGanas", "#ElRitmoNosUne", "#PaQueLoGocen", "#AquíNadieSeRinde",
  "#ElComboCompleto", "#DeUnaConToda", "#UnidosEnElBeat", "#LaFuerzaDelEquipo", "#TodosALaCarga"
];

const frases_centro = [
  "🚀 Solo se llega más rápido, pero en equipo TNA se llega más lejos.",
  "🏆 El talento gana partidos, pero el trabajo en equipo TNA y la inteligencia ganan campeonatos.",
  "🤝 Ninguno de nosotros en TNA es tan bueno como todos nosotros juntos.",
  "🧠 En TNA, un equipo no es solo quien trabaja junto, es quien confía plenamente entre sí.",
  "🔄 En TNA, la colaboración divide el trabajo y multiplica los resultados.",
  "📚 El conocimiento en TNA crece cuando se comparte.",
  "🧭 Un líder TNA no crea seguidores, crea más líderes.",
  "🎯 Lo que hacemos juntos en TNA, lo logramos mejor.",
  "🐟 Si me das pescado, comeré una vez; si me enseñas a pescar como en TNA, comeré toda la vida.",
  "🏅 No intentes ser tú el mejor de TNA, intenta que TNA sea el mejor equipo.",
  "👷‍♂️ En TNA, cada proyecto es una oportunidad para demostrar lo que somos capaces de construir juntos.",
  "🌐 En TNA no solo conectamos redes. Conectamos personas, ideas y soluciones que hacen la diferencia.",
  "💎 Nuestro compromiso en TNA no es solo con la tecnología, sino con la excelencia en cada entrega.",
  "🔥 Cuando cada uno da lo mejor, TNA alcanza lo extraordinario.",
  "🚧 En TNA, no hay logros pequeños: todo lo que hacemos impulsa a nuestros clientes a avanzar con confianza.",
  "💪 En TNA somos tan fuertes como nuestra colaboración. Y en equipo, somos imparables.",
  "🛠️ Cada desafío técnico en TNA es una oportunidad para demostrar por qué nuestros clientes confían en nosotros.",
  "🌟 En TNA, la excelencia no es un acto, es un hábito. Gracias por hacerlo posible cada día.",
  "🎯 Tecnología con propósito, soluciones con impacto. Ese es el sello TNA.",
  "📈 En TNA seguimos creciendo porque nunca dejamos de aprender, colaborar y superar expectativas.",
  "🧠 El conocimiento nos distingue en TNA, pero la actitud es lo que nos impulsa.",
  "✅ Cada solución entregada por TNA refleja compromiso y calidad de todo el equipo.",
  "🚀 En TNA avanzamos con tecnología y crecemos con personas comprometidas.",
  "🔋 Tu trabajo tiene impacto. Tu esfuerzo potencia la transformación digital que TNA ofrece a sus clientes.",
  "💡 La innovación en TNA comienza con una actitud: la de nunca conformarse.",
  "🤩 Detrás de cada cliente satisfecho, hay un equipo TNA que dio lo mejor.",
  "🧩 Un gran equipo TNA no es el que evita los problemas, sino el que los resuelve con inteligencia y unión.",
  "⚙️ En TNA hacemos que lo complejo funcione. Y lo hacemos con excelencia.",
  "🎖️ Lo que hoy parece un reto, mañana será un logro gracias a tu dedicación en TNA.",
  "🏗️ En TNA no solo trabajamos. Dejamos huella en cada red que diseñamos, protegemos y optimizamos."
];

function fraseAleatoria(arr){ return arr[Math.floor(Math.random()*arr.length)]; }

document.addEventListener("DOMContentLoaded",()=>{
  const f1=document.getElementById("frase-centro");
  const f2=document.getElementById("frase-motivacional");
  if(f1)f1.textContent=fraseAleatoria(frases_centro);
  if(f2)f2.textContent=fraseAleatoria(frases);
});
</script>

<?php
// ==========================================================
// 🔁 AUTO-REFRESH Persistente
// ==========================================================
require_once __DIR__ . '/session_core.php';
if (isset($_POST['auto_refresh'])) {
    $_SESSION['auto_refresh'] = ($_POST['auto_refresh'] === 'true');
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) exit;
}
$autoRefreshState = !empty($_SESSION['auto_refresh']);
?>

<script>
function iniciarAutoRefreshHeader() {
  const REFRESH_INTERVAL = 300000; // 5 minutos
  let autoRefreshTimer = null;
  const toggle = document.getElementById('autoRefreshToggle');
  const barra = document.getElementById('auto-refresh-bar');

  if (!toggle || !barra) {
    setTimeout(iniciarAutoRefreshHeader, 300);
    return;
  }

  toggle.checked = <?php echo $autoRefreshState ? 'true' : 'false'; ?>;
  if (toggle.checked) iniciarAutoRefresh();

  toggle.addEventListener('change', function() {
    const activo = this.checked;
    guardarEstadoSesion(activo);
    if (activo) iniciarAutoRefresh();
    else detenerAutoRefresh();
  });

  function guardarEstadoSesion(activo) {
    fetch("", {
      method: "POST",
      headers: { "X-Requested-With": "XMLHttpRequest" },
      body: new URLSearchParams({ auto_refresh: activo })
    });
  }

  function iniciarAutoRefresh() {
    detenerAutoRefresh();
    autoRefreshTimer = setInterval(() => location.reload(), REFRESH_INTERVAL);
  }

  function detenerAutoRefresh() {
    if (autoRefreshTimer) clearInterval(autoRefreshTimer);
  }
}

document.addEventListener("DOMContentLoaded", iniciarAutoRefreshHeader);
</script>
