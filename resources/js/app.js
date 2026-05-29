import './bootstrap';
import Alpine from 'alpinejs';
import { createIcons, BarChart3, Bolt, CheckCircle2, Clipboard, Clock3, Eye, EyeOff, FileText, History, Loader2, Lock, LogOut, MessageSquare, Moon, PanelLeft, Search, Settings, Sparkles, Sun, Tags, Trash2, Type, User, WandSparkles, X } from 'lucide';

window.Alpine = Alpine;

const lucideIcons = { BarChart3, Bolt, CheckCircle2, Clipboard, Clock3, Eye, EyeOff, FileText, History, Loader2, Lock, LogOut, MessageSquare, Moon, PanelLeft, Search, Settings, Sparkles, Sun, Tags, Trash2, Type, User, WandSparkles, X };

Alpine.data('theme', () => ({
    dark: localStorage.theme === 'dark',
    init() {
        this.apply();
        this.$watch('dark', () => this.apply());
    },
    apply() {
        document.documentElement.classList.toggle('dark', this.dark);
        localStorage.theme = this.dark ? 'dark' : 'light';
    },
    toggle() {
        this.dark = !this.dark;
    },
}));

Alpine.data('copyable', () => ({
    copied: false,
    async copy(value) {
        await navigator.clipboard.writeText(value);
        this.copied = true;
        setTimeout(() => this.copied = false, 1400);
    },
}));

Alpine.data('submitState', () => ({
    loading: false,
    currentStage: 0,
    progress: 8,
    timer: null,
    stages: [
        {
            title: 'Validating input',
            detail: 'Memastikan judul dan deskripsi siap diproses.',
            icon: 'check-circle-2',
            progress: 14,
        },
        {
            title: 'Reading channel context',
            detail: 'Menggabungkan niche, tone, bahasa, dan CTA channel.',
            icon: 'file-text',
            progress: 28,
        },
        {
            title: 'Mapping SEO keywords',
            detail: 'Mencari intent utama dan kata kunci paling relevan.',
            icon: 'search',
            progress: 46,
        },
        {
            title: 'Optimizing titles',
            detail: 'Membuat judul dengan penekanan CAPSLOCK yang natural.',
            icon: 'type',
            progress: 64,
        },
        {
            title: 'Building metadata',
            detail: 'Menyusun deskripsi, top 10 meta tags, dan hashtags.',
            icon: 'tags',
            progress: 82,
        },
        {
            title: 'Finalizing package',
            detail: 'Menyiapkan pin comment dan menyimpan hasil ke history.',
            icon: 'message-square',
            progress: 94,
        },
    ],
    get activeStage() {
        return this.stages[this.currentStage];
    },
    submit() {
        this.loading = true;
        this.currentStage = 0;
        this.progress = this.stages[0].progress;
        this.$nextTick(() => createIcons({ icons: lucideIcons }));

        this.timer = setInterval(() => {
            if (this.currentStage < this.stages.length - 1) {
                this.currentStage += 1;
                this.progress = this.stages[this.currentStage].progress;
                this.$nextTick(() => createIcons({ icons: lucideIcons }));
            }
        }, 1800);

        this.$root.submit();
    },
}));

Alpine.data('competitorAnalysisState', () => ({
    loading: false,
    currentStage: 0,
    progress: 10,
    stages: [
        {
            title: 'Reading seed keyword',
            detail: 'Memahami channel kompetitor dan range analisis.',
            icon: 'search',
            progress: 18,
        },
        {
            title: 'Resolving channel',
            detail: 'Mencari channel ID, handle, dan playlist upload.',
            icon: 'file-text',
            progress: 38,
        },
        {
            title: 'Fetching uploads',
            detail: 'Mengambil video sesuai range waktu yang dipilih.',
            icon: 'bar-chart-3',
            progress: 58,
        },
        {
            title: 'Analyzing schedule',
            detail: 'Menghitung pola jam upload, hari upload, dan frekuensi.',
            icon: 'clock-3',
            progress: 78,
        },
        {
            title: 'Extracting title keywords',
            detail: 'Mencari keyword yang paling sering muncul pada judul.',
            icon: 'type',
            progress: 94,
        },
    ],
    get activeStage() {
        return this.stages[this.currentStage];
    },
    submit() {
        this.loading = true;
        this.currentStage = 0;
        this.progress = this.stages[0].progress;
        this.$nextTick(() => createIcons({ icons: lucideIcons }));

        setInterval(() => {
            if (this.currentStage < this.stages.length - 1) {
                this.currentStage += 1;
                this.progress = this.stages[this.currentStage].progress;
                this.$nextTick(() => createIcons({ icons: lucideIcons }));
            }
        }, 1700);

        this.$root.submit();
    },
}));

Alpine.start();

createIcons({
    icons: lucideIcons,
});
