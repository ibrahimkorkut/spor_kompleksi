<?php
/**
 * KVKK & GDPR Uyumlu Çerez Tercih Banner'ı ve Granüler Liquid Glass Modalı
 *
 * @package ApexArena
 */
?>
<div class="apex-cookie-banner" id="apex-cookie-banner" role="region" aria-label="Çerez bildirimi" hidden>
    <div class="apex-cookie-text">
        <strong>Çerez Tercihleriniz ve KVKK Bilgilendirmesi</strong><br>
        Apex Arena olarak rezervasyon oturumunuzu güvenli tutmak ve erişilebilirlik tercihlerinizi hatırlamak için çerezler kullanıyoruz.
        <a href="<?php echo esc_url( home_url( '/cerez-politikasi/' ) ); ?>" style="color:var(--apex-text);text-decoration:underline;">Çerez Politikası</a> ·
        <a href="<?php echo esc_url( home_url( '/kvkk-aydinlatma-metni/' ) ); ?>" style="color:var(--apex-text);text-decoration:underline;">KVKK Metni</a>
    </div>
    <div class="apex-cookie-actions">
        <button type="button" class="apex-btn apex-btn-ghost apex-btn-sm" data-open-cookie-modal>Tercihleri Yönet</button>
        <button type="button" class="apex-btn apex-btn-outline apex-btn-sm" id="apex-cookie-essential">Sadece Zorunlular</button>
        <button type="button" class="apex-btn apex-btn-primary apex-btn-sm" id="apex-cookie-accept-all">Tümünü Kabul Et</button>
    </div>
</div>

<div class="apex-modal-backdrop" id="apex-cookie-modal" role="dialog" aria-modal="true" aria-labelledby="cookie-modal-h" hidden>
    <div class="apex-modal">
        <div class="apex-modal-head">
            <div>
                <span class="apex-eyebrow" style="margin:0;">6698 Sayılı KVKK Uyumlu</span>
                <h2 id="cookie-modal-h" class="apex-h3">Çerez Tercihlerini Yönet</h2>
            </div>
            <button type="button" class="apex-mobile-close" data-close-modal="apex-cookie-modal" aria-label="Kapat">✕</button>
        </div>

        <div style="display:grid;gap:12px;">
            <div style="background:rgba(255,255,255,0.05);border:1px solid var(--apex-border);border-radius:12px;padding:14px;display:flex;justify-content:space-between;gap:14px;align-items:center;">
                <div>
                    <strong>1. Zorunlu Çerezler</strong>
                    <div class="apex-muted" style="font-size:0.83rem;">Üye girişi, güvenlik (CSRF nonce), salon ve PT rezervasyon oturumlarının çalışması için gereklidir.</div>
                </div>
                <span class="apex-tag apex-tag-volt">Her Zaman Aktif</span>
            </div>

            <label style="background:rgba(255,255,255,0.05);border:1px solid var(--apex-border);border-radius:12px;padding:14px;display:flex;justify-content:space-between;gap:14px;align-items:center;cursor:pointer;">
                <div>
                    <strong>2. Tercih ve Erişilebilirlik Çerezleri</strong>
                    <div class="apex-muted" style="font-size:0.83rem;">Yazı boyutu, yüksek kontrast, disleksi fontu ve AI Spor Koçu tercihlerinizi hatırlar.</div>
                </div>
                <input type="checkbox" id="cookie-pref-functional" checked style="width:20px;height:20px;accent-color:var(--apex-crimson);">
            </label>

            <label style="background:rgba(255,255,255,0.05);border:1px solid var(--apex-border);border-radius:12px;padding:14px;display:flex;justify-content:space-between;gap:14px;align-items:center;cursor:pointer;">
                <div>
                    <strong>3. Performans ve Analitik Çerezler</strong>
                    <div class="apex-muted" style="font-size:0.83rem;">Sayfa hızını ve salon yoğunluk saatlerini anonim olarak ölçmemize yardımcı olur.</div>
                </div>
                <input type="checkbox" id="cookie-pref-analytics" checked style="width:20px;height:20px;accent-color:var(--apex-crimson);">
            </label>

            <label style="background:rgba(255,255,255,0.05);border:1px solid var(--apex-border);border-radius:12px;padding:14px;display:flex;justify-content:space-between;gap:14px;align-items:center;cursor:pointer;">
                <div>
                    <strong>4. Kampanya ve Kişiselleştirme Çerezleri</strong>
                    <div class="apex-muted" style="font-size:0.83rem;">Size özel PT paket indirimleri ve turnuva duyuruları için kullanılır.</div>
                </div>
                <input type="checkbox" id="cookie-pref-marketing" style="width:20px;height:20px;accent-color:var(--apex-crimson);">
            </label>
        </div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:20px;">
            <button type="button" class="apex-btn apex-btn-primary" id="apex-cookie-save-prefs" style="flex:1;">Tercihleri Kaydet</button>
            <button type="button" class="apex-btn apex-btn-outline" id="apex-cookie-modal-accept-all">Tümünü Kabul Et</button>
        </div>
    </div>
</div>
