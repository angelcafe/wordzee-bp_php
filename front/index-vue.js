import {createApp} from 'vue';
const app = createApp({
    data() {
        return {
            version: '0.4',
        }
    },
    methods: {
        getPL(punto, linea) {
            let devolver = {
                name: `pal${linea}let${punto}`,
            };
            if (linea === 6 && punto === 6) {
                devolver.class = 'btn btn-secondary bg-primary';
                devolver.value = 'DP';
            } else if (linea === 7 && punto === 7) {
                devolver.class = 'btn btn-secondary bg-warning';
                devolver.value = 'TP';
            } else {
                devolver.class = 'btn btn-secondary info me-1';
            }
            return devolver;
        },
        getRonda(ele, ronda) {
            if (ele === 'label') {
                return {
                    class: 'form-check-label',
                    for: `ronda${ronda}`,
                }
            } else {
                return {
                    class: "form-check-input",
                    name: 'ronda',
                    type: 'radio',
                    value: ronda,
                }
            }
        }
    }
});
app.mount('#app');