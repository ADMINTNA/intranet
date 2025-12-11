<?php
// ==========================================================
// kickoff_ajax/cm_header.php
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
body,td,th { font-family: Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif; margin: 0; padding: 0; }
a { color: #000000; }

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
/* ==========================================================
   BOTÓN TOUCHBAR – Acceso a versión Beta AJAX
   ========================================================== */

.touchbar-beta-btn {
    background: white;               /* estilo touchbar clásico */
    color: #512554;                  /* violeta corporativo */
    border: 2px solid #512554;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.25s ease;
    white-space: nowrap;
}

.touchbar-beta-btn:hover {
    background: #512554;
    color: white;
    transform: translateY(-2px);
}

.touchbar-beta-btn:active {
    transform: translateY(0px);
}
    
</style>

<!-- ========================================================== -->
<!-- 🧭 FUNCIÓN GLOBAL DE SALIDA FUERA DEL IFRAME -->
<!-- ========================================================== -->
<script>
// Redirige SIEMPRE al marco principal, no dentro del iframe
function salirDelSistema() {

    if (!confirm("¿Deseas cerrar tu sesión y salir del sistema?")) {
        return false;
    }

    // Rompe el marco y redirige al login principal
    window.top.location.href = "https://intranet.icontel.cl/index.php";
    return false; 
}
    
function abrirBetaKickoff() {
    // Abrir nueva ventana pero ocultar URL completa al usuario
    let nueva = window.open(
        "https://intranet.icontel.cl/kickoff_ajax/icontel.php",
        "_blank",
        "noopener"
    );

    // Fuerza a mostrar URL limpia
    if (nueva) {
        nueva.opener = null;
        setTimeout(() => {
            try {
                nueva.history.replaceState({}, "", "https://intranet.icontel.cl");
            } catch (e) {
                console.warn("No se pudo limpiar la URL, pero no afecta el funcionamiento.");
            }
        }, 500);
    }
}    
</script>

<table class="no-sort" cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse; margin: 0; padding: 0;">
  <tbody>
    <tr>
      <td valign="top" style="padding:0;margin:0;border:none;border-spacing:0;">
        <table class="no-sort" border="0" cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse;">
          <tbody>
            <!-- 🧩 Fila 2 -->
            <tr id="Fila_1" style="height: 140px; color:white;background-image: url('images/icontel_header.jpg'); background-size: 100% 100%;">
              <td colspan="2"></td>
              <td style="vertical-align: top">
                <select style="color:white;font-weight:bold;text-decoration:underline;background-color:transparent;border:none;"
                        name="calculadoras" id="calculadoras" onchange="manejarCambio(this)">
                  <option value="">Calculadoras</option>
                  <option value="https://intranet.icontel.cl/fotovoltaico/">Fotovoltaica</option>
                </select>
              </td>

              <td style="vertical-align: top">
                <select style="color:white;font-weight:bold;text-decoration:underline;background-color:transparent;border:none;"
                        name="formularios" id="formulario" onchange="manejarCambio(this)">
                  <option value="">Formularios</option>
                  <option value="https://sweet.icontel.cl/custom/New_lead/new_lead.html">Referidos</option>
                  <option value="https://forms.gle/KckuqAywwEk755KU9">Fotovoltaico</option>
                </select>
              </td>

              <td style="vertical-align: top">
                <select style="color:white;font-weight:bold;text-decoration:underline;background-color:transparent;border:none;"
                        name="materiales" id="materiales" onchange="manejarCambio(this)">
                  <option value="">Marketing y Escalamiento</option>
                  <option value="https://drive.google.com/drive/folders/1caqwj2gJSbA0VYAg9MVWlJz5SogPjM2i?usp=sharing">Material/Marketing</option>
                  <option value="/images/organigrama.jpg">Organigrama TNA</option>
                  <option value="https://drive.google.com/drive/folders/1AWTF-sgYBQiKrQrHN8SdVgTwdyr5E_LU?usp=sharing">Escalamiento Proveedor</option>
                </select>
              </td>

              <td style="vertical-align: top" align="center">
                <a style="color:white;font-weight:bold;" href="https://calendar.app.google/9oeDbrLGtM3Gzhn28" target="_blank">
                  Agendamiento Comercial
                </a>
              </td>

              <td style="vertical-align: top" align="center">
                <a style="color:white;font-weight:bold;" href="https://www.tnagroup.cl/telecomunicaciones/presentacion" target="_blank">
                  Presentación Empresa
                </a>
              </td>

              <td align="center" style="vertical-align: top; white-space:nowrap;">
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
    <!--button class="touchbar-beta-btn" onclick="abrirBetaKickoff()">
        🚀 Beta KickOff
    </button-->
</td>            </tr>

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
if (session_status() === PHP_SESSION_NONE) session_start();
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
