<?php
/**
 * Companion Plugin Auto-Updater & One-Click Installer (Best Practice WordPress).
 *
 * Otomatis memperbarui plugin pendamping 'credible-company-core' ketika versi
 * terpasang lebih lama daripada paket bundled di dalam tema, serta menyediakan
 * instalasi/update/aktivasi 1-klik melalui API resmi WordPress (Plugin_Upgrader).
 *
 * @package CredibleCompany
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CC_CORE_PLUGIN_SLUG', 'credible-company-core/credible-company-core.php' );
define( 'CC_CORE_PLUGIN_ZIP', get_template_directory() . '/bundled-plugins/credible-company-core.zip' );
define( 'CC_CORE_BUNDLED_VERSION', '1.0.3' );

/**
 * Ambil metadata plugin pendamping dari wp-content/plugins/.
 *
 * @param bool $refresh Bersihkan cache daftar plugin terlebih dahulu.
 * @return array<string, mixed>
 */
function cc_get_core_plugin_data( $refresh = false ) {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	if ( $refresh && function_exists( 'wp_clean_plugins_cache' ) ) {
		wp_clean_plugins_cache( true );
	}
	$plugins = get_plugins();
	return isset( $plugins[ CC_CORE_PLUGIN_SLUG ] ) && is_array( $plugins[ CC_CORE_PLUGIN_SLUG ] )
		? $plugins[ CC_CORE_PLUGIN_SLUG ]
		: array();
}

/**
 * Cek apakah plugin pendamping sudah terpasang di wp-content/plugins/.
 *
 * @param bool $refresh Bersihkan cache daftar plugin terlebih dahulu.
 * @return bool
 */
function cc_is_core_plugin_installed( $refresh = false ) {
	return ! empty( cc_get_core_plugin_data( $refresh ) );
}

/**
 * Ambil versi plugin pendamping yang terpasang di disk.
 *
 * @param bool $refresh Bersihkan cache daftar plugin terlebih dahulu.
 * @return string
 */
function cc_get_installed_core_plugin_version( $refresh = false ) {
	$data = cc_get_core_plugin_data( $refresh );
	if ( ! empty( $data['Version'] ) ) {
		return (string) $data['Version'];
	}
	if ( defined( 'CC_CORE_VERSION' ) ) {
		return (string) CC_CORE_VERSION;
	}
	return '';
}

/**
 * Cek apakah plugin pendamping terpasang tetapi versinya tertinggal dari bundled ZIP.
 *
 * @param bool $refresh Bersihkan cache daftar plugin terlebih dahulu.
 * @return bool
 */
function cc_is_core_plugin_outdated( $refresh = false ) {
	$installed_version = cc_get_installed_core_plugin_version( $refresh );
	if ( '' === $installed_version ) {
		return false;
	}
	return version_compare( $installed_version, CC_CORE_BUNDLED_VERSION, '<' );
}

/**
 * Cek apakah plugin pendamping sedang aktif.
 *
 * @return bool
 */
function cc_is_core_plugin_active() {
	if ( defined( 'CC_CORE_ACTIVE' ) ) {
		return true;
	}
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	return is_plugin_active( CC_CORE_PLUGIN_SLUG );
}

/**
 * Key transient cooldown jika auto-update gagal pada versi bundled tertentu.
 *
 * @return string
 */
function cc_get_core_autoupdate_lock_key() {
	return 'cc_core_autoupdate_failed_' . md5( CC_CORE_BUNDLED_VERSION );
}

/**
 * Pasang atau perbarui plugin pendamping langsung dari bundled-plugins/credible-company-core.zip.
 *
 * @param bool $activate_if_inactive Aktifkan plugin jika sebelumnya tidak aktif.
 * @return string|WP_Error 'updated', 'installed', atau WP_Error saat gagal.
 */
function cc_install_or_update_core_plugin_package( $activate_if_inactive = true ) {
	if ( defined( 'DISALLOW_FILE_MODS' ) && DISALLOW_FILE_MODS ) {
		return new WP_Error(
			'disallow_file_mods',
			__( 'Modifikasi file dinonaktifkan oleh DISALLOW_FILE_MODS.', 'crediblecompany' )
		);
	}

	if ( ! file_exists( CC_CORE_PLUGIN_ZIP ) ) {
		return new WP_Error(
			'missing_zip',
			__( 'Paket ZIP plugin pendamping tidak ditemukan di tema.', 'crediblecompany' )
		);
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/misc.php';
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

	$is_update  = cc_is_core_plugin_installed( true );
	$was_active = $is_update && is_plugin_active( CC_CORE_PLUGIN_SLUG );

	if ( ! WP_Filesystem() ) {
		return new WP_Error(
			'filesystem_unavailable',
			__( 'WP_Filesystem membutuhkan kredensial FTP/SSH sehingga update otomatis tidak dapat berjalan di latar belakang.', 'crediblecompany' )
		);
	}

	$skin     = new Automatic_Upgrader_Skin();
	$upgrader = new Plugin_Upgrader( $skin );
	$result   = $upgrader->install(
		CC_CORE_PLUGIN_ZIP,
		array(
			'overwrite_package' => true,
		)
	);

	if ( is_wp_error( $result ) ) {
		return $result;
	}

	if ( ! $result ) {
		if ( method_exists( $skin, 'get_errors' ) && is_wp_error( $skin->get_errors() ) && $skin->get_errors()->has_errors() ) {
			return $skin->get_errors();
		}
		return new WP_Error(
			'extract_failed',
			__( 'Gagal mengekstrak paket plugin pendamping.', 'crediblecompany' )
		);
	}

	if ( function_exists( 'wp_clean_plugins_cache' ) ) {
		wp_clean_plugins_cache( true );
	}

	if ( function_exists( 'wp_opcache_invalidate_directory' ) ) {
		wp_opcache_invalidate_directory( WP_PLUGIN_DIR . '/credible-company-core' );
	}

	$should_activate = $was_active || ( $activate_if_inactive && ! is_plugin_active( CC_CORE_PLUGIN_SLUG ) );
	if ( $should_activate && ! is_plugin_active( CC_CORE_PLUGIN_SLUG ) ) {
		$activated = activate_plugin( CC_CORE_PLUGIN_SLUG, '', false, $is_update );
		if ( is_wp_error( $activated ) ) {
			return $activated;
		}
	}

	delete_transient( cc_get_core_autoupdate_lock_key() );

	return $is_update ? 'updated' : 'installed';
}

/**
 * Jalankan auto-update plugin pendamping apabila terpasang versi lebih lama.
 *
 * @return void
 */
function cc_maybe_auto_update_core_plugin() {
	if ( defined( 'DISALLOW_FILE_MODS' ) && DISALLOW_FILE_MODS ) {
		return;
	}

	if ( ! file_exists( CC_CORE_PLUGIN_ZIP ) ) {
		return;
	}

	if ( ! cc_is_core_plugin_installed() || ! cc_is_core_plugin_outdated() ) {
		return;
	}

	if ( false !== get_transient( cc_get_core_autoupdate_lock_key() ) ) {
		return;
	}

	$result = cc_install_or_update_core_plugin_package( false );
	if ( is_wp_error( $result ) ) {
		set_transient( cc_get_core_autoupdate_lock_key(), $result->get_error_message(), 10 * MINUTE_IN_SECONDS );
		return;
	}

	if ( 'updated' === $result ) {
		set_transient( 'cc_core_just_auto_updated', CC_CORE_BUNDLED_VERSION, 5 * MINUTE_IN_SECONDS );
	}
}

/**
 * Otomatis update plugin pendamping saat tema diperbarui melalui WordPress Upgrader.
 *
 * @param WP_Upgrader $upgrader Instance upgrader.
 * @param array       $hook_extra Konteks update WordPress.
 * @return void
 */
add_action( 'upgrader_process_complete', 'cc_auto_update_core_plugin_after_theme_upgrade', 20, 2 );
function cc_auto_update_core_plugin_after_theme_upgrade( $upgrader, $hook_extra ) {
	if ( empty( $hook_extra['type'] ) || 'theme' !== $hook_extra['type'] ) {
		return;
	}

	$current_theme = get_stylesheet();
	$themes        = array();
	if ( ! empty( $hook_extra['themes'] ) && is_array( $hook_extra['themes'] ) ) {
		$themes = $hook_extra['themes'];
	} elseif ( ! empty( $hook_extra['theme'] ) ) {
		$themes = array( $hook_extra['theme'] );
	}

	if ( ! empty( $themes ) && ! in_array( $current_theme, $themes, true ) ) {
		return;
	}

	delete_transient( cc_get_core_autoupdate_lock_key() );
	cc_maybe_auto_update_core_plugin();
}

/**
 * Otomatis update plugin pendamping pada load admin berikutnya setelah update tema (ZIP upload/FTP).
 *
 * @return void
 */
add_action( 'admin_init', 'cc_auto_update_core_plugin_on_admin', 20 );
function cc_auto_update_core_plugin_on_admin() {
	if ( wp_doing_ajax() || wp_doing_cron() || ( defined( 'IFRAME_REQUEST' ) && IFRAME_REQUEST ) ) {
		return;
	}

	if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}

	if ( isset( $_GET['action'] ) && 'cc_install_core_plugin' === $_GET['action'] ) {
		return;
	}

	cc_maybe_auto_update_core_plugin();
}

/**
 * Tampilkan Admin Notice jika plugin pendamping belum aktif, baru auto-update, atau butuh update manual.
 */
add_action( 'admin_notices', 'cc_core_plugin_admin_notice' );
function cc_core_plugin_admin_notice() {
	$auto_updated_version = get_transient( 'cc_core_just_auto_updated' );
	if ( $auto_updated_version && ! cc_is_core_plugin_outdated( true ) && current_user_can( 'update_plugins' ) ) {
		delete_transient( 'cc_core_just_auto_updated' );
		echo '<div class="notice notice-success is-dismissible"><p>';
		printf(
			/* translators: %s: updated bundled version */
			esc_html__( 'Plugin Credible Company Core otomatis diperbarui ke versi %s.', 'crediblecompany' ),
			esc_html( (string) $auto_updated_version )
		);
		echo '</p></div>';
	}

	if ( isset( $_GET['cc_core_error'] ) && current_user_can( 'activate_plugins' ) ) {
		echo '<div class="notice notice-error is-dismissible"><p>';
		echo esc_html( sanitize_text_field( wp_unslash( $_GET['cc_core_error'] ) ) );
		echo '</p></div>';
	}

	if ( cc_is_core_plugin_active() ) {
		if ( isset( $_GET['cc_core_status'] ) && 'activated' === $_GET['cc_core_status'] ) {
			echo '<div class="notice notice-success is-dismissible"><p>';
			esc_html_e( 'Plugin Credible Company Core berhasil dipasang dan diaktifkan.', 'crediblecompany' );
			echo '</p></div>';
		} elseif ( isset( $_GET['cc_core_status'] ) && 'updated' === $_GET['cc_core_status'] ) {
			echo '<div class="notice notice-success is-dismissible"><p>';
			printf(
				/* translators: %s: updated bundled version */
				esc_html__( 'Plugin Credible Company Core berhasil diperbarui ke versi %s.', 'crediblecompany' ),
				esc_html( CC_CORE_BUNDLED_VERSION )
			);
			echo '</p></div>';
		} elseif ( cc_is_core_plugin_outdated( true ) && current_user_can( 'update_plugins' ) ) {
			$installed_ver = cc_get_installed_core_plugin_version( true );
			$action_url    = wp_nonce_url(
				admin_url( 'admin-post.php?action=cc_install_core_plugin' ),
				'cc_install_core_plugin_nonce'
			);
			$lock_error    = get_transient( cc_get_core_autoupdate_lock_key() );

			echo '<div class="notice notice-warning is-dismissible"><p>';
			printf(
				/* translators: 1: active version, 2: bundled version */
				esc_html__( 'Pembaruan plugin Credible Company Core tersedia (%1$s → %2$s).', 'crediblecompany' ),
				esc_html( $installed_ver ),
				esc_html( CC_CORE_BUNDLED_VERSION )
			);
			if ( is_string( $lock_error ) && '' !== $lock_error ) {
				echo ' <em>(' . esc_html( $lock_error ) . ')</em>';
			}
			echo '</p>';
			if ( ( ! defined( 'DISALLOW_FILE_MODS' ) || ! DISALLOW_FILE_MODS ) && file_exists( CC_CORE_PLUGIN_ZIP ) ) {
				echo '<p><a href="' . esc_url( $action_url ) . '" class="button button-primary">';
				esc_html_e( 'Update Credible Company Core Sekarang', 'crediblecompany' );
				echo '</a></p>';
			}
			echo '</div>';
		}
		return;
	}

	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	// Jangan tampilkan notice di layar proses update/install itu sendiri
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( $screen && in_array( $screen->id, array( 'update', 'update-core' ), true ) ) {
		return;
	}

	$is_installed = cc_is_core_plugin_installed( true );
	$is_outdated  = $is_installed && cc_is_core_plugin_outdated( true );
	$action_url   = wp_nonce_url(
		admin_url( 'admin-post.php?action=cc_install_core_plugin' ),
		'cc_install_core_plugin_nonce'
	);

	echo '<div class="notice notice-warning">';
	echo '<p><strong>' . esc_html__( 'Tema Credible Company membutuhkan plugin pendamping:', 'crediblecompany' ) . '</strong> ';
	esc_html_e( 'Plugin "Credible Company Core" diperlukan untuk fitur Testimoni, Paket Jasa, Tim Marketing, SEO, Keamanan, dan Optimasi Performa.', 'crediblecompany' );
	echo '</p>';

	if ( $is_installed && ! $is_outdated ) {
		echo '<p><a href="' . esc_url( $action_url ) . '" class="button button-primary">';
		esc_html_e( 'Aktifkan Credible Company Core', 'crediblecompany' );
		echo '</a></p>';
	} elseif ( defined( 'DISALLOW_FILE_MODS' ) && DISALLOW_FILE_MODS ) {
		echo '<p><em>' . esc_html__( 'DISALLOW_FILE_MODS aktif di wp-config.php. Silakan pasang/perbarui folder credible-company-core secara manual ke wp-content/plugins/.', 'crediblecompany' ) . '</em></p>';
	} elseif ( ( $is_outdated ? current_user_can( 'update_plugins' ) : current_user_can( 'install_plugins' ) ) && file_exists( CC_CORE_PLUGIN_ZIP ) ) {
		echo '<p><a href="' . esc_url( $action_url ) . '" class="button button-primary">';
		echo $is_outdated
			? esc_html__( 'Update & Aktifkan Credible Company Core', 'crediblecompany' )
			: esc_html__( 'Install & Aktifkan Credible Company Core', 'crediblecompany' );
		echo '</a></p>';
	}
	echo '</div>';
}

/**
 * Handler One-Click Install, Update & Activate via admin-post.php.
 */
add_action( 'admin_post_cc_install_core_plugin', 'cc_handle_install_core_plugin' );
function cc_handle_install_core_plugin() {
	check_admin_referer( 'cc_install_core_plugin_nonce' );

	if ( ! current_user_can( 'activate_plugins' ) ) {
		wp_die( esc_html__( 'Anda tidak memiliki izin untuk mengaktifkan plugin.', 'crediblecompany' ), 403 );
	}

	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	delete_transient( cc_get_core_autoupdate_lock_key() );

	$is_installed = cc_is_core_plugin_installed( true );
	$is_outdated  = $is_installed && cc_is_core_plugin_outdated( true );
	$status       = 'activated';

	if ( ! $is_installed || $is_outdated ) {
		$required_cap = $is_outdated ? 'update_plugins' : 'install_plugins';
		if ( ! current_user_can( $required_cap ) ) {
			wp_die( esc_html__( 'Anda tidak memiliki izin untuk memasang atau memperbarui plugin.', 'crediblecompany' ), 403 );
		}

		$result = cc_install_or_update_core_plugin_package( true );
		if ( is_wp_error( $result ) ) {
			wp_safe_redirect( add_query_arg( 'cc_core_error', rawurlencode( $result->get_error_message() ), admin_url() ) );
			exit;
		}
		$status = 'updated' === $result ? 'updated' : 'activated';
	} elseif ( ! is_plugin_active( CC_CORE_PLUGIN_SLUG ) ) {
		$activate = activate_plugin( CC_CORE_PLUGIN_SLUG );
		if ( is_wp_error( $activate ) ) {
			wp_safe_redirect( add_query_arg( 'cc_core_error', rawurlencode( $activate->get_error_message() ), admin_url() ) );
			exit;
		}
	}

	wp_safe_redirect( add_query_arg( 'cc_core_status', $status, admin_url() ) );
	exit;
}
