<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (116 columnas, ~35655 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('auditoria_vehiculo', function (Blueprint $table) {
            $table->increments('id');
            $table->string('identificador', 255);
            $table->string('supervisor', 255);
            $table->timestamp('fecha_relevo');
            $table->string('tecnico', 255);
            $table->date('fecha');
            $table->string('ypf_ruta', 255);
            $table->string('credencial_claro', 255);
            $table->string('vtv', 255);
            $table->string('seguro', 255);
            $table->string('oblea_gnc', 255);
            $table->string('func_nafta', 255);
            $table->string('func_gnc', 255);
            $table->string('balizas', 255);
            $table->string('bateria', 255);
            $table->string('dv_auxilio', 50);
            $table->string('dv_balizas', 50);
            $table->string('dv_cedula', 50);
            $table->string('dv_criquet', 50);
            $table->string('dv_dominio', 50);
            $table->string('dv_llave_cruz', 50);
            $table->string('dv_matafuego', 50);
            $table->string('dv_modelo', 50);
            $table->string('i_aire', 50);
            $table->string('i_balizas', 50);
            $table->string('i_bocina', 50);
            $table->string('i_calefaccion', 50);
            $table->string('i_encendedor', 50);
            $table->string('i_giro_del_acom', 50);
            $table->string('i_giro_del_conductor', 50);
            $table->string('i_giro_tras_acom', 50);
            $table->string('i_giro_tras_conductor', 50);
            $table->string('i_limpiaparabrisa', 50);
            $table->string('i_tapa_fusilera', 50);
            $table->string('i_temperatura', 50);
            $table->string('i_velocimetro', 50);
            $table->string('i_llave', 50);
            $table->string('tor_manijas_giro', 50);
            $table->string('tor_manijas_luces', 50);
            $table->string('tor_pulsadores', 50);
            $table->string('tor_rejilla_ventilacion', 50);
            $table->string('extfre_capot', 50);
            $table->string('extfre_cubiertas', 50);
            $table->string('extfre_llantas', 50);
            $table->string('extfre_opticas', 50);
            $table->string('extfre_parabrisas', 50);
            $table->string('extfre_paragolpe', 50);
            $table->string('extfre_parrilla', 50);
            $table->string('extfre_portaescalera', 50);
            $table->string('exttras_baul', 50);
            $table->string('exttras_cano_esc', 50);
            $table->string('exttras_cerradura', 50);
            $table->string('exttras_cubiertas', 50);
            $table->string('exttras_llantas', 50);
            $table->string('exttras_luneta', 50);
            $table->string('exttras_opticas', 50);
            $table->string('exttras_paragolpe', 50);
            $table->string('intcond_alfombra_del', 50);
            $table->string('intcond_alfombra_tras', 50);
            $table->string('intcond_apoya_brazo_del', 50);
            $table->string('intcond_apoya_brazo_tras', 50);
            $table->string('intcond_butaca_del', 50);
            $table->string('intcond_butaca_tras', 50);
            $table->string('intcond_cerradura_del', 50);
            $table->string('intcond_cerradura_tras', 50);
            $table->string('intcond_manija_puerta_del', 50);
            $table->string('intcond_manija_puerta_tras', 50);
            $table->string('intcond_manija_ventanilla_del', 50);
            $table->string('intcond_manija_ventanilla_tras', 50);
            $table->string('intcond_panel_puerta_del', 50);
            $table->string('intcond_panel_puerta_tras', 50);
            $table->string('intcond_panel_techo', 50);
            $table->string('intcond_polarizado_del', 50);
            $table->string('intcond_polarizado_tras', 50);
            $table->string('intcond_seguro_puerta_del', 50);
            $table->string('intcond_seguro_puerta_tras', 50);
            $table->string('intacom_butaca_del', 50);
            $table->string('intacom_butaca_tras', 50);
            $table->string('intacom_cerradura_del', 50);
            $table->string('intacom_cerradura_tras', 50);
            $table->string('intacom_manija_puerta_del', 50);
            $table->string('intacom_manija_puerta_tras', 50);
            $table->string('intacom_manija_ventanilla_del', 50);
            $table->string('intacom_manija_ventanilla_tras', 50);
            $table->string('intacom_panel_puerta_del', 50);
            $table->string('intacom_panel_puerta_tras', 50);
            $table->string('intacom_panel_techo', 50);
            $table->string('intacom_seguro_puerta_del', 50);
            $table->string('intacom_seguro_puerta_tras', 50);
            $table->string('extcond_bagueta_del', 50);
            $table->string('extcond_bagueta_tras', 50);
            $table->string('extcond_cerradura_del', 50);
            $table->string('extcond_cerradura_tras', 50);
            $table->string('extcond_espejo_lateral', 50);
            $table->string('extcond_espejo_retrovisor', 50);
            $table->string('extcond_manija_puerta_del', 50);
            $table->string('extcond_manija_puerta_tras', 50);
            $table->string('extcond_tapa_tanque', 50);
            $table->string('extcond_ventanilla_del', 50);
            $table->string('extcond_ventanilla_tras', 50);
            $table->string('extacom_bagueta_del', 50);
            $table->string('extacom_bagueta_tras', 50);
            $table->string('extacom_cerradura_del', 50);
            $table->string('extacom_cerradura_tras', 50);
            $table->string('extacom_espejo_lateral', 50);
            $table->string('extacom_manija_puerta_del', 50);
            $table->string('extacom_manija_puerta_tras', 50);
            $table->string('extacom_ventanilla_del', 50);
            $table->string('extacom_ventanilla_tras', 50);
            $table->string('extacom_higuiene_int', 50);
            $table->string('extacom_higuiene_ext', 50);
            $table->text('obs');
            $table->string('imagenpri', 255);
            $table->string('imagenseg', 255);
            $table->string('imagenter', 255);
            $table->string('imagencuar', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria_vehiculo');
    }
};
