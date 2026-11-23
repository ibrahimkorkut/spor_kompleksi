<?php
/**
 * Custom Post Types & Meta Boxes: Antrenörler, PT Paketleri, Salonlar, Salon Duyuruları
 *
 * @package ApexArena
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function apex_register_post_types() {
    // 1. Antrenörler (PT & Branş Antrenörleri)
    register_post_type( 'apex_trainer', [
        'labels' => [
            'name'          => __( 'Antrenörler', 'apex-arena' ),
            'singular_name' => __( 'Antrenör', 'apex-arena' ),
            'add_new_item'  => __( 'Yeni Antrenör Ekle', 'apex-arena' ),
            'edit_item'     => __( 'Antrenörü Düzenle', 'apex-arena' ),
            'menu_name'     => __( 'Antrenörler', 'apex-arena' ),
        ],
        'public'        => true,
        'has_archive'   => true,
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-groups',
        'menu_position' => 21,
        'rewrite'       => [ 'slug' => 'antrenor' ],
        'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
    ] );

    // 2. PT Paketleri & Eğitimler (Bireysel / Grupsal)
    register_post_type( 'apex_pt_package', [
        'labels' => [
            'name'          => __( 'PT Paketleri', 'apex-arena' ),
            'singular_name' => __( 'PT Paketi', 'apex-arena' ),
            'add_new_item'  => __( 'Yeni PT Paketi Ekle', 'apex-arena' ),
            'edit_item'     => __( 'PT Paketini Düzenle', 'apex-arena' ),
            'menu_name'     => __( 'PT Paketleri', 'apex-arena' ),
        ],
        'public'        => true,
        'has_archive'   => false,
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-awards',
        'menu_position' => 22,
        'rewrite'       => [ 'slug' => 'pt-paket' ],
        'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
    ] );

    // 3. Spor Salonları (2x Basketbol + 1x Voleybol)
    register_post_type( 'apex_court', [
        'labels' => [
            'name'          => __( 'Salonlar', 'apex-arena' ),
            'singular_name' => __( 'Salon', 'apex-arena' ),
            'add_new_item'  => __( 'Yeni Salon Ekle', 'apex-arena' ),
            'edit_item'     => __( 'Salonu Düzenle', 'apex-arena' ),
            'menu_name'     => __( 'Salonlar', 'apex-arena' ),
        ],
        'public'        => true,
        'has_archive'   => false,
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-building',
        'menu_position' => 23,
        'rewrite'       => [ 'slug' => 'salon' ],
        'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
    ] );

    // 4. Salon Duyuruları & Etkinlikler
    register_post_type( 'apex_announcement', [
        'labels' => [
            'name'          => __( 'Salon Duyuruları', 'apex-arena' ),
            'singular_name' => __( 'Duyuru', 'apex-arena' ),
            'add_new_item'  => __( 'Yeni Duyuru Ekle', 'apex-arena' ),
            'edit_item'     => __( 'Duyuruyu Düzenle', 'apex-arena' ),
            'menu_name'     => __( 'Salon Duyuruları', 'apex-arena' ),
        ],
        'public'        => true,
        'has_archive'   => true,
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-megaphone',
        'menu_position' => 24,
        'rewrite'       => [ 'slug' => 'duyuru' ],
        'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
    ] );
}
add_action( 'init', 'apex_register_post_types' );

/**
 * Meta Kutuları (WP Admin'den tüm alanların kolayca yönetilmesi için)
 */
function apex_add_meta_boxes() {
    add_meta_box( 'apex_trainer_meta', 'Antrenör Profili, Sertifikalar & Online Takvim', 'apex_render_trainer_meta_box', 'apex_trainer', 'normal', 'high' );
    add_meta_box( 'apex_package_meta', 'PT Paketi, Ücret & Eğitim Günleri Detayları', 'apex_render_package_meta_box', 'apex_pt_package', 'normal', 'high' );
    add_meta_box( 'apex_court_meta', 'Salon Teknik Bilgileri, Ücret & Hakem Opsiyonu', 'apex_render_court_meta_box', 'apex_court', 'normal', 'high' );
    add_meta_box( 'apex_announcement_meta', 'Duyuru Kategorisi & Geçerlilik', 'apex_render_announcement_meta_box', 'apex_announcement', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'apex_add_meta_boxes' );

function apex_render_trainer_meta_box( $post ) {
    wp_nonce_field( 'apex_meta_save', 'apex_meta_nonce' );
    $fields = [
        '_apex_role'         => get_post_meta( $post->ID, '_apex_role', true ),
        '_apex_specialty'    => get_post_meta( $post->ID, '_apex_specialty', true ),
        '_apex_experience'   => get_post_meta( $post->ID, '_apex_experience', true ),
        '_apex_education'    => get_post_meta( $post->ID, '_apex_education', true ),
        '_apex_session_fee'  => get_post_meta( $post->ID, '_apex_session_fee', true ),
        '_apex_rating'       => get_post_meta( $post->ID, '_apex_rating', true ),
        '_apex_modes'        => get_post_meta( $post->ID, '_apex_modes', true ),
        '_apex_photo'        => get_post_meta( $post->ID, '_apex_photo', true ),
        '_apex_certificates' => get_post_meta( $post->ID, '_apex_certificates', true ),
        '_apex_schedule'     => get_post_meta( $post->ID, '_apex_schedule', true ),
    ];
    ?>
    <table class="form-table">
        <tr><th>Ünvan</th><td><input type="text" name="_apex_role" class="regular-text" value="<?php echo esc_attr( $fields['_apex_role'] ); ?>" placeholder="Head PT & Atletik Performans Koçu"></td></tr>
        <tr><th>Uzmanlık Alanı</th><td><input type="text" name="_apex_specialty" class="large-text" value="<?php echo esc_attr( $fields['_apex_specialty'] ); ?>"></td></tr>
        <tr><th>Deneyim (Yıl)</th><td><input type="number" name="_apex_experience" class="small-text" value="<?php echo esc_attr( $fields['_apex_experience'] ); ?>"></td></tr>
        <tr><th>Eğitim / Mezuniyet</th><td><input type="text" name="_apex_education" class="large-text" value="<?php echo esc_attr( $fields['_apex_education'] ); ?>"></td></tr>
        <tr><th>Tek Seans PT Ücreti (₺)</th><td><input type="number" name="_apex_session_fee" class="small-text" value="<?php echo esc_attr( $fields['_apex_session_fee'] ); ?>"></td></tr>
        <tr><th>Değerlendirme Puanı (1-5)</th><td><input type="text" name="_apex_rating" class="small-text" value="<?php echo esc_attr( $fields['_apex_rating'] ?: '4.9' ); ?>"></td></tr>
        <tr><th>Eğitim Türleri (bireysel,grupsal)</th><td><input type="text" name="_apex_modes" class="regular-text" value="<?php echo esc_attr( $fields['_apex_modes'] ?: 'bireysel,grupsal' ); ?>"></td></tr>
        <tr><th>Tema Görsel Dosyası</th><td><input type="text" name="_apex_photo" class="regular-text" value="<?php echo esc_attr( $fields['_apex_photo'] ); ?>" placeholder="trainer-kerem.jpg"></td></tr>
        <tr><th>Sertifikalar (JSON)</th><td><textarea name="_apex_certificates" rows="4" class="large-text code"><?php echo esc_textarea( $fields['_apex_certificates'] ); ?></textarea></td></tr>
        <tr><th>Haftalık Online Takvim (JSON)</th><td><textarea name="_apex_schedule" rows="4" class="large-text code"><?php echo esc_textarea( $fields['_apex_schedule'] ); ?></textarea></td></tr>
    </table>
    <?php
}

function apex_render_package_meta_box( $post ) {
    wp_nonce_field( 'apex_meta_save', 'apex_meta_nonce' );
    $m = fn( $k ) => get_post_meta( $post->ID, $k, true );
    ?>
    <table class="form-table">
        <tr><th>Antrenman Türü</th><td>
            <select name="_apex_training_type">
                <option value="bireysel" <?php selected( $m( '_apex_training_type' ), 'bireysel' ); ?>>Bireysel Antrenman</option>
                <option value="grupsal" <?php selected( $m( '_apex_training_type' ), 'grupsal' ); ?>>Grupsal Antrenman</option>
            </select>
        </td></tr>
        <tr><th>Seans Sayısı</th><td><input type="number" name="_apex_sessions" class="small-text" value="<?php echo esc_attr( $m( '_apex_sessions' ) ); ?>"></td></tr>
        <tr><th>Seans Başı Ücret (₺)</th><td><input type="number" name="_apex_per_session_fee" class="small-text" value="<?php echo esc_attr( $m( '_apex_per_session_fee' ) ); ?>"></td></tr>
        <tr><th>Toplam Paket Fiyatı (₺)</th><td><input type="number" name="_apex_total_price" class="small-text" value="<?php echo esc_attr( $m( '_apex_total_price' ) ); ?>"></td></tr>
        <tr><th>Seans Süresi (Dakika)</th><td><input type="number" name="_apex_duration" class="small-text" value="<?php echo esc_attr( $m( '_apex_duration' ) ?: 60 ); ?>"></td></tr>
        <tr><th>Eğitim Günleri & Saatleri</th><td><input type="text" name="_apex_days" class="large-text" value="<?php echo esc_attr( $m( '_apex_days' ) ); ?>"></td></tr>
        <tr><th>Seviye / Kontenjan</th><td><input type="text" name="_apex_level" class="regular-text" value="<?php echo esc_attr( $m( '_apex_level' ) ); ?>"></td></tr>
        <tr><th>Kapsam & İçerik (Her satır 1 madde)</th><td><textarea name="_apex_scope" rows="5" class="large-text"><?php echo esc_textarea( $m( '_apex_scope' ) ); ?></textarea></td></tr>
        <tr><th>Öne Çıkan Paket</th><td><input type="checkbox" name="_apex_featured" value="1" <?php checked( $m( '_apex_featured' ), '1' ); ?>> En Popüler Rozeti Göster</td></tr>
    </table>
    <?php
}

function apex_render_court_meta_box( $post ) {
    wp_nonce_field( 'apex_meta_save', 'apex_meta_nonce' );
    $m = fn( $k ) => get_post_meta( $post->ID, $k, true );
    ?>
    <table class="form-table">
        <tr><th>Branş</th><td>
            <select name="_apex_sport">
                <option value="basketbol" <?php selected( $m( '_apex_sport' ), 'basketbol' ); ?>>Basketbol</option>
                <option value="voleybol" <?php selected( $m( '_apex_sport' ), 'voleybol' ); ?>>Voleybol</option>
            </select>
        </td></tr>
        <tr><th>Salon Kodu</th><td><input type="text" name="_apex_court_code" class="regular-text" value="<?php echo esc_attr( $m( '_apex_court_code' ) ); ?>"></td></tr>
        <tr><th>Ölçüler</th><td><input type="text" name="_apex_dimensions" class="regular-text" value="<?php echo esc_attr( $m( '_apex_dimensions' ) ); ?>"></td></tr>
        <tr><th>Zemin Standardı</th><td><input type="text" name="_apex_surface" class="regular-text" value="<?php echo esc_attr( $m( '_apex_surface' ) ); ?>"></td></tr>
        <tr><th>Oyuncu / Tribün Kapasitesi</th><td><input type="text" name="_apex_capacity" class="regular-text" value="<?php echo esc_attr( $m( '_apex_capacity' ) ); ?>"></td></tr>
        <tr><th>Saatlik Ücret (₺)</th><td><input type="number" name="_apex_hourly_rate" class="small-text" value="<?php echo esc_attr( $m( '_apex_hourly_rate' ) ); ?>"></td></tr>
        <tr><th>Hakem Hizmeti Ücreti (₺)</th><td><input type="number" name="_apex_referee_fee" class="small-text" value="<?php echo esc_attr( $m( '_apex_referee_fee' ) ?: 450 ); ?>"></td></tr>
        <tr><th>Ana Görsel Dosyası</th><td><input type="text" name="_apex_photo" class="regular-text" value="<?php echo esc_attr( $m( '_apex_photo' ) ); ?>"></td></tr>
        <tr><th>Özellikler (Her satır 1 madde)</th><td><textarea name="_apex_features" rows="4" class="large-text"><?php echo esc_textarea( $m( '_apex_features' ) ); ?></textarea></td></tr>
    </table>
    <?php
}

function apex_render_announcement_meta_box( $post ) {
    wp_nonce_field( 'apex_meta_save', 'apex_meta_nonce' );
    $m = fn( $k ) => get_post_meta( $post->ID, $k, true );
    ?>
    <p><label><strong>Kategori:</strong></label><br>
        <select name="_apex_ann_category" style="width:100%;">
            <?php foreach ( [ 'Turnuva', 'Salon Duyurusu', 'Kampanya', 'Grup Dersi', 'Bakım & Saatler' ] as $cat ) : ?>
                <option value="<?php echo esc_attr( $cat ); ?>" <?php selected( $m( '_apex_ann_category' ), $cat ); ?>><?php echo esc_html( $cat ); ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <p><label><strong>Önem Derecesi:</strong></label><br>
        <select name="_apex_ann_priority" style="width:100%;">
            <option value="normal" <?php selected( $m( '_apex_ann_priority' ), 'normal' ); ?>>Genel</option>
            <option value="highlight" <?php selected( $m( '_apex_ann_priority' ), 'highlight' ); ?>>Öne Çıkan</option>
            <option value="urgent" <?php selected( $m( '_apex_ann_priority' ), 'urgent' ); ?>>Önemli / Acil</option>
        </select>
    </p>
    <p><label><strong>İlgili Salon / Birim:</strong></label><br>
        <input type="text" name="_apex_ann_location" style="width:100%;" value="<?php echo esc_attr( $m( '_apex_ann_location' ) ); ?>">
    </p>
    <p><label><strong>Etkinlik / Geçerlilik Tarihi:</strong></label><br>
        <input type="text" name="_apex_ann_date" style="width:100%;" value="<?php echo esc_attr( $m( '_apex_ann_date' ) ); ?>" placeholder="30.11.2026">
    </p>
    <?php
}

function apex_save_meta_boxes( $post_id ) {
    if ( ! isset( $_POST['apex_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['apex_meta_nonce'] ) ), 'apex_meta_save' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $text_keys = [
        '_apex_role', '_apex_specialty', '_apex_experience', '_apex_education', '_apex_session_fee',
        '_apex_rating', '_apex_modes', '_apex_photo', '_apex_training_type', '_apex_sessions',
        '_apex_per_session_fee', '_apex_total_price', '_apex_duration', '_apex_days', '_apex_level',
        '_apex_sport', '_apex_court_code', '_apex_dimensions', '_apex_surface', '_apex_capacity',
        '_apex_hourly_rate', '_apex_referee_fee', '_apex_ann_category', '_apex_ann_priority',
        '_apex_ann_location', '_apex_ann_date',
    ];
    foreach ( $text_keys as $k ) {
        if ( isset( $_POST[ $k ] ) ) {
            update_post_meta( $post_id, $k, sanitize_text_field( wp_unslash( $_POST[ $k ] ) ) );
        }
    }

    $textarea_keys = [ '_apex_certificates', '_apex_schedule', '_apex_scope', '_apex_features' ];
    foreach ( $textarea_keys as $k ) {
        if ( isset( $_POST[ $k ] ) ) {
            update_post_meta( $post_id, $k, sanitize_textarea_field( wp_unslash( $_POST[ $k ] ) ) );
        }
    }

    if ( get_post_type( $post_id ) === 'apex_pt_package' ) {
        update_post_meta( $post_id, '_apex_featured', isset( $_POST['_apex_featured'] ) ? '1' : '0' );
    }
}
add_action( 'save_post', 'apex_save_meta_boxes' );
