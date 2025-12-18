/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./application/views/**/*.php",
    "./application/views/**/*.html",
    "./assets/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        /* ================= PAGE & LAYOUT ================= */
        "page-bg": "#FFFFFF", // background utama
        "navbar-bg": "#0F766E", // navbar / footer (teal)
        "border-default": "#E5E7EB", // border abu halus

        /* ================= CARD & SECTION ================= */
        "card-bg": "#DFEDFBFF", // ⭐ card abu kebiruan lembut
        "card-hover": "#F1F5F9", // hover card
        "section-bg": "#F9FAFB", // pembungkus section

        /* ================= TEXT ================= */
        "text-heading": "#111827", // judul (bold)
        "text-subheading": "#1F2937", // semi-bold
        "text-content": "#374151", // paragraf
        "text-caption": "#6B7280", // caption / helper
        "text-muted": "#9CA3AF", // hint kecil

        /* ================= BUTTON ================= */
        "btn-primary": "#0F766E", // tombol utama
        "btn-hover": "#115E59", // hover
        "btn-soft": "#ECFEFF", // button soft / outline
        "btn-text": "#FFFFFF", // teks button

        /* ================= ACCENT ================= */
        "accent-primary": "#14B8A6", // link aktif / highlight
        "accent-soft": "#99F6E4", // badge / icon bg

        /* ================= STATUS ================= */
        "status-success": "#16A34A",
        "status-warning": "#F59E0B",
        "status-error": "#DC2626",
        /* ================= DROPDOWN ================= */
        "dropdown-bg": "#ECFEFF", // background option
        "dropdown-border": "#5EEAD4", // border dropdown
        "dropdown-text": "#134E4A", // text option (teal gelap)
        "dropdown-selected": "#0F766E", // text saat dipilih

        /* ================= WARNA CADANGAN ================= */
      },
    },
  },
  plugins: [],
};
