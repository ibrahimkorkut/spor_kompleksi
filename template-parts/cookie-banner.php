<?php
/**
 * KVKK & GDPR Uyumlu Çerez Tercih Banner'ı ve Granüler Yönetim Modalı
 *
 * @package ApexArena
 */
?>
<!-- Alt Çerez Onay Şeridi -->
<div class="apex-cookie-banner" id="apex-cookie-banner" role="region" aria-label="Çerez bildirimi" hidden>
    <div class="apex-cookie-text">
        <strong>🍪 Çerez Tercihleriniz & KVKK Bilgilendirmesi</strong><br>
        APEX ARENA olarak deneyiminizi iyileştirmek, salon rezervasyon oturumunuzu güvenli tutmak ve erişilebilirlik tercihlerinizi hatırlamak için çerezler kullanıyoruz.
        <a href="<?php echo esc_url( home_url( '/cerez-politikasi/' ) ); ?>" style="color:var(--apex-volt);text-decoration:underline;">Çerez Politikası</a> ·
        <a href="<?php echo esc_url( home_url( '/kvkk-aydinlatma-metni/' ) ); ?>" style="color:var(--apex-volt);text-decoration:underline;">KVKK Metni</a>
    </div>
    <div class="apex-cookie-actions">
        <button type="button" class="apex-btn apex-btn-ghost apex-btn-sm" data-open-cookie-modal>TERCİHLERİ YÖNET</button>
        <button type="button" class="apex-btn apex-btn-outline apex-btn-sm" id="apex-cookie-essential">SADECE ZORUNLULAR</button>
        <button type="button" class="apex-btn apex-btn-primary apex-btn-sm" id="apex-cookie-accept-all">TÜMÜNÜ KABUL ET</button>
    </div>
</div>

<!-- Detaylı Çerez Tercihleri Modalı -->
<div class="apex-modal-backdrop" id="apex-cookie-modal" role="dialog" aria-modal="true" aria-labelledby="cookie-modal-h" hidden>
    <div class="apex-modal">
        <div class="apex-modal-head">
            <div>
                <span class="apex-eyebrow" style="margin:0;">6698 SAYILI KVKK UYUMLU</span>
                <h2 id="cookie-modal-h" class="apex-h3">ÇEREZ TERCİHLERİNİ YÖNET</h2>
            </div>
            <button type="button" class="apex-mobile-close" data-close-modal="apex-cookie-modal" aria-label="Kapat">✕</button>
        </div>

        <div style="display:grid;gap:14px;">
            <div style="background:var(--apex-bg);border:1px solid var(--apex-border);border-radius:10px;padding:14px;display:flex;justify-content:space-between;gap:14px;align-items:center;">
                <div>
                    <strong>1. Zorunlu Çerezler (Her Zaman Aktif)</strong>
                    <div class="apex-muted" style="font-size:0.84rem;">Üye girişi, güvenlik (CSRF nonce), salon ve PT rezervasyon oturumlarının çalışması için gereklidir.</div>
                </div>
                <span class="apex-tag apex-tag-volt">ZORUNLU</span>
            </div>

            <label style="background:var(--apex-bg);border:1px solid var(--apex-border);border-radius:10px;padding:14px;display:flex;justify-content:space-between;gap:14px;align-items:center;cursor:pointer;">
                <div>
                    <strong>2. Tercih & Erişilebilirlik Çerezleri</strong>
                    <div class="apex-muted" style="font-size:0.84rem;">Yazı boyutu, yüksek kontrast, disleksi fontu ve AI Spor Koçu sohbet tercihlerinizi hatırlar.</div>
                </div>
                <input type="checkbox" id="cookie-pref-functional" checked style="width:22px;height:22px;accent-color:var(--apex-crimson);">
            </label>

            <label style="background:var(--apex-bg);border:1px solid var(--apex-border);border-radius:10px;padding:14px;display:flex;justify-content:space-between;gap:14px;align-items:center;cursor:pointer;">
                <div>
                    <strong>3. Performans & Analitik Çerezler</strong>
                    <div class="apex-muted" style="font-size:0.84rem;">Sayfa hızını ve en çok kullanılan salon saatlerini anonim olarak analiz etmemize yardımcı olur.</div>
                </div>
                <input type="checkbox" id="cookie-pref-analytics" checked style="width:22px;height:22px;accent-color:var(--apex-crimson);">
            </label>

            <label style="background:var(--apex-bg);border:1px solid var(--apex-border);border-radius:10px;padding:14px;display:flex;justify-content:space-between;gap:14px;align-items:center;cursor:pointer;">
                <div>
                    <strong>4. Kampanya & Kişiselleştirme Çerezleri</strong>
                    <div class="apex-muted" style="font-size:0.84rem;">Size özel PT paket indirimleri ve turnuva duyurularının gösterilmesi için kullanılır.</div>
                </div>
                <input type="checkbox" id="cookie-pref-marketing" style="width:22px;height:22px;accent-color:var(--apex-crimson);">
            </label>
        </div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:20px;">
            <button type="button" class="apex-btn apex-btn-primary" id="apex-cookie-save-prefs" style="flex:1;">TERCİHLERİ KAYDET</button>
            <button type="button" class="apex-btn apex-btn-volt" id="apex-cookie-modal-accept-all">TÜMÜNÜ KABUL ET</button>
        </div>
    </div>
</div>
