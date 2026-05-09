<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <style>
        :root {
            color-scheme: light;
            --bg: #f3efe6;
            --panel: #fffdf8;
            --ink: #1f2933;
            --muted: #52606d;
            --line: #d9cbb8;
            --brand: #9a3412;
            --brand-dark: #7c2d12;
            --accent: #164e63;
            --ok: #166534;
            --warn: #991b1b;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(154, 52, 18, 0.12), transparent 32%),
                linear-gradient(135deg, #f8f4ec, var(--bg));
            color: var(--ink);
        }
        .shell {
            width: min(1120px, calc(100% - 32px));
            margin: 32px auto;
            display: grid;
            gap: 24px;
        }
        .hero, .panel {
            background: rgba(255, 253, 248, 0.9);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(217, 203, 184, 0.9);
            border-radius: 24px;
            box-shadow: 0 16px 50px rgba(31, 41, 51, 0.08);
        }
        .hero {
            padding: 32px;
        }
        .hero h1 {
            margin: 0 0 12px;
            font-size: clamp(2rem, 5vw, 3.4rem);
            line-height: 1;
        }
        .hero p {
            max-width: 720px;
            color: var(--muted);
            font-size: 1.05rem;
        }
        .hero code {
            color: var(--accent);
            font-weight: 700;
        }
        .grid {
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            gap: 24px;
        }
        .panel {
            padding: 24px;
        }
        .panel h2 {
            margin-top: 0;
            font-size: 1.2rem;
        }
        label {
            display: block;
            margin-bottom: 14px;
            font-size: 0.95rem;
        }
        input, textarea, select {
            width: 100%;
            margin-top: 6px;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 12px 14px;
            font: inherit;
            background: #fff;
        }
        textarea { min-height: 104px; resize: vertical; }
        .row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }
        .row-3 {
            display: grid;
            grid-template-columns: 1.3fr 1fr 1fr;
            gap: 10px;
        }
        .item {
            padding: 12px;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: #fffaf2;
            margin-bottom: 12px;
        }
        button {
            border: 0;
            border-radius: 999px;
            padding: 12px 18px;
            font: inherit;
            cursor: pointer;
            transition: transform 150ms ease, opacity 150ms ease;
        }
        button:hover { transform: translateY(-1px); }
        .primary {
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            color: #fff;
        }
        .secondary {
            background: #fff;
            border: 1px solid var(--line);
        }
        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .flash {
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 14px;
            font-size: 0.92rem;
        }
        .flash.ok {
            background: rgba(22, 101, 52, 0.1);
            color: var(--ok);
        }
        .flash.error {
            background: rgba(153, 27, 27, 0.1);
            color: var(--warn);
        }
        .list {
            display: grid;
            gap: 14px;
        }
        .card {
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 16px;
            background: #fff;
        }
        .card header {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            margin-bottom: 12px;
        }
        .badge {
            display: inline-flex;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(22, 78, 99, 0.1);
            color: var(--accent);
            font-size: 0.82rem;
            text-transform: uppercase;
        }
        .meta {
            color: var(--muted);
            font-size: 0.92rem;
        }
        .empty {
            color: var(--muted);
            border: 1px dashed var(--line);
            border-radius: 18px;
            padding: 18px;
            text-align: center;
        }
        @media (max-width: 900px) {
            .grid, .row, .row-3 { grid-template-columns: 1fr; }
            .shell { width: min(100% - 20px, 1120px); }
            .hero, .panel { padding: 20px; }
        }
    </style>
</head>
<body>
    <div id="app" class="shell">
        <section class="hero">
            <p>Urbano Express Challenge</p>
            <h1>Recepción y consulta de pedidos</h1>
        </section>

        <section class="grid">
            <article class="panel">
                <h2>Nuevo pedido</h2>

                <div v-if="flash.message" class="flash" :class="flash.type">
                    @{{ flash.message }}
                </div>

                <form @submit.prevent="submitOrder">
                    <div class="row">
                        <label>
                            ID externo
                            <input v-model="form.external_id" placeholder="shopify-1001">
                        </label>
                        <label>
                            Estado
                            <select v-model="form.status">
                                <option value="pending">Pendiente</option>
                                <option value="processing">En Proceso</option>
                                <option value="shipped">Enviado</option>
                                <option value="delivered">Entregado</option>
                            </select>
                        </label>
                    </div>

                    <div class="row">
                        <label>
                            Cliente
                            <input v-model="form.customer_name" required>
                        </label>
                        <label>
                            Email
                            <input v-model="form.customer_email" type="email" required>
                        </label>
                    </div>

                    <label>
                        Dirección de envío
                        <textarea v-model="form.shipping_address" required></textarea>
                    </label>

                    <div class="row">
                        <label>
                            Moneda
                            <input v-model="form.currency" maxlength="3" required>
                        </label>
                    </div>

                    <h3>Items</h3>
                    <div v-for="(item, index) in form.items" :key="index" class="item">
                        <div class="row-3">
                            <label>
                                SKU
                                <input v-model="item.sku" required>
                            </label>
                            <label>
                                Cantidad
                                <input v-model.number="item.quantity" type="number" min="1" required>
                            </label>
                            <label>
                                Precio
                                <input v-model.number="item.price" type="number" min="0" step="0.01" required>
                            </label>
                        </div>
                        <label>
                            Nombre
                            <input v-model="item.name" required>
                        </label>
                        <button v-if="form.items.length > 1" class="secondary" type="button" @click="removeItem(index)">Quitar item</button>
                    </div>

                    <label>
                        Total del pedido
                        <input :value="formattedOrderTotal" type="text" readonly>
                    </label>

                    <div class="actions">
                        <button class="secondary" type="button" @click="addItem">Agregar item</button>
                        <button class="primary" type="submit" :disabled="loading">
                            @{{ loading ? 'Enviando...' : 'Crear pedido' }}
                        </button>
                    </div>
                </form>
            </article>

            <article class="panel">
                <div class="actions" style="justify-content: space-between; margin-bottom: 16px;">
                    <h2>Pedidos registrados</h2>
                    <button class="secondary" type="button" @click="loadOrders" :disabled="loadingOrders">
                        @{{ loadingOrders ? 'Actualizando...' : 'Refrescar' }}
                    </button>
                </div>

                <div v-if="orders.length" class="list">
                    <article v-for="order in orders" :key="order.id" class="card">
                        <header>
                            <div>
                                <strong>#@{{ order.id }}</strong>
                                <div class="meta">@{{ order.customer_name }} · @{{ order.customer_email }}</div>
                            </div>
                            <span class="badge">@{{ order.status }}</span>
                        </header>
                        <div class="meta">Externo: @{{ order.external_id || 'sin referencia' }}</div>
                        <div class="meta">Total: @{{ order.currency }} @{{ Number(order.total_amount).toFixed(2) }}</div>
                        <div class="meta">Dirección: @{{ order.shipping_address }}</div>
                        <div class="meta">Items: @{{ order.items.map(item => `${item.quantity}x ${item.name}`).join(', ') }}</div>
                    </article>
                </div>

                <div v-else class="empty">
                    No hay pedidos cargados todavía.
                </div>
            </article>
        </section>
    </div>

    <script>
        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    apiToken: @json(config('app.order_api_token')),
                    apiBase: '/api/orders',
                    loading: false,
                    loadingOrders: false,
                    orders: [],
                    flash: { message: '', type: 'ok' },
                    form: this.emptyForm(),
                };
            },
            mounted() {
                this.loadOrders();
            },
            computed: {
                orderTotal() {
                    return this.form.items.reduce((sum, item) => {
                        const quantity = Number(item.quantity) || 0;
                        const price = Number(item.price) || 0;

                        return sum + (quantity * price);
                    }, 0);
                },
                formattedOrderTotal() {
                    return this.orderTotal.toFixed(2);
                },
            },
            methods: {
                emptyForm() {
                    return {
                        external_id: '',
                        customer_name: '',
                        customer_email: '',
                        shipping_address: '',
                        status: 'pending',
                        currency: 'ARS',
                        total_amount: 0,
                        items: [{ sku: '', name: '', quantity: 1, price: 0 }],
                    };
                },
                headers() {
                    return {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${this.apiToken}`,
                    };
                },
                addItem() {
                    this.form.items.push({ sku: '', name: '', quantity: 1, price: 0 });
                },
                removeItem(index) {
                    this.form.items.splice(index, 1);
                },
                showFlash(message, type = 'ok') {
                    this.flash = { message, type };
                },
                async loadOrders() {
                    this.loadingOrders = true;
                    try {
                        const response = await fetch(this.apiBase, {
                            headers: this.headers(),
                        });
                        const payload = await response.json();

                        if (!response.ok) {
                            throw new Error(payload.message || 'No se pudieron obtener los pedidos.');
                        }

                        this.orders = payload.data;
                    } catch (error) {
                        this.showFlash(error.message, 'error');
                    } finally {
                        this.loadingOrders = false;
                    }
                },
                async submitOrder() {
                    this.loading = true;
                    this.flash.message = '';

                    try {
                        const orderPayload = {
                            ...this.form,
                            total_amount: Number(this.orderTotal.toFixed(2)),
                        };

                        const response = await fetch(this.apiBase, {
                            method: 'POST',
                            headers: this.headers(),
                            body: JSON.stringify(orderPayload),
                        });

                        const responsePayload = await response.json();

                        if (!response.ok) {
                            throw new Error(responsePayload.message || 'No se pudo crear el pedido.');
                        }

                        this.showFlash('Pedido creado correctamente.', 'ok');
                        this.form = this.emptyForm();
                        await this.loadOrders();
                    } catch (error) {
                        this.showFlash(error.message, 'error');
                    } finally {
                        this.loading = false;
                    }
                },
            },
        }).mount('#app');
    </script>
</body>
</html>
