<template>
    <div class="border-t border-gray-200 space-y-3">
        <div class="flex justify-between py-2">
            <div class="space-y-2">
                <p class="text-lg text-black font-semibold">Price</p>
                <p class="font-semibold text-lg"> ${{ priceInDollars }}</p>
            </div>
            <div class="flex flex-col space-y-2 text-lg text-black font-semibold">
                <label for="quantity">Qty</label>
                <input type="text" id="quantity" name="quantity" v-model="quantity"
                       class="border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent p-2">
            </div>
        </div>
        <button class="w-full bg-blue-400 text-white p-2 rounded font-semibold text-lg" @click.prevent="openStripe">Buy Ticket</button>
    </div>
</template>

<script>
      export default {
        props: {
            price : Number,
            concertTitle : String,
            concertId : Number
        },
        data() {
            return {
                quantity: 1,
                stripeHandler: null,
            }
        },
        computed: {
            description() {
                if (this.quantity > 1) {
                    return `${this.quantity} tickets to ${this.concertTitle}`
                }
                return `One ticket to ${this.concertTitle}`
            },
            totalPrice() {
                return this.quantity * this.price
            },
            priceInDollars() {
                return (this.price).toFixed(2)
            },
            totalPriceInDollars() {
                return (this.totalPrice).toFixed(2)
            },
        },
        methods: {
            initStripe() {
                const handler = StripeCheckout.configure({
                    key: window.stripeKey
                })

                window.addEventListener('popstate', () => {
                    handler.close()
                })

                return handler
            },
            openStripe(callback) {
                this.stripeHandler.open({
                    name: 'Ticket App',
                    description: this.description,
                    currency: "usd",
                    allowRememberMe: false,
                    panelLabel: 'Pay {{amount}}',
                    amount : this.totalPrice,
                    token : this.buyTicket,
                })
            },
            buyTicket(token) {
                console.log({
                    email: token.email,
                    quantity: this.quantity,
                    payment_token: token.id,
                })

                this.processing = true

                axios.post(`/concerts/${this.concertId}/orders`, {
                    email: token.email,
                    ticket_quantity: this.quantity,
                    payment_token: token.id,
                }).then(response => {
                    console.log('Charge succeeded.') 
                    this.processing = false
                }).catch(response => {
                    this.processing = false
                })
            }
        },
        created() {
            this.stripeHandler = this.initStripe()
            console.log(this.concertTitle);
            
        }
    }
</script>
