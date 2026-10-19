
document.addEventListener('alpine:init', () => {
    Alpine.data('cpfMask', () => ({
        format(value) {
            let cpf = value.replace(/\D/g, '').slice(0, 11);

            return cpf
                .replace(/^(\d{3})(\d)/, '$1.$2')
                .replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3')
                .replace(/\.(\d{3})(\d)/, '.$1-$2');
        },

        init() {
            this.$el.addEventListener('input', () => {
                const formatted = this.format(this.$el.value);

                this.$el.value = formatted;

                this.$el.dispatchEvent(
                    new Event('input', { bubbles: true })
                );
            });
        }
    }));
});
