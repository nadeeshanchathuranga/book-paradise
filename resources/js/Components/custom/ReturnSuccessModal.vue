<template>
    <TransitionRoot as="template" :show="open" static>
        <Dialog class="relative z-20" static>
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
                leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click.stop />
            </TransitionChild>

            <div class="fixed inset-0 z-10 flex items-center justify-center">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 scale-95"
                    enter-to="opacity-100 scale-100" leave="ease-in duration-200" leave-from="opacity-100 scale-100"
                    leave-to="opacity-0 scale-95">
                    <DialogPanel
                        class="bg-white border-4 border-blue-600 rounded-[20px] shadow-xl max-w-xl w-full p-6 text-center">
                        <DialogTitle class="text-4xl font-bold">Return Successful</DialogTitle>

                        <div class="w-full h-full flex flex-col justify-center items-center space-y-4 mt-4">
                            <p class="text-2xl text-black">Refund amount: {{ Number(total || 0).toFixed(2) }} LKR</p>
                            <img src="/images/checked.png" class="h-20 object-cover" />
                        </div>

                        <div class="flex justify-center items-center space-x-3 pt-6 mt-4">
                            <p @click="handlePrint"
                                class="cursor-pointer bg-blue-600 text-white font-bold uppercase tracking-wider px-4 py-3 rounded-xl">
                                Print Return Receipt
                            </p>
                            <p @click="$emit('update:open', false)"
                                class="cursor-pointer bg-red-600 text-white font-bold uppercase tracking-wider px-4 py-3 rounded-xl">
                                Close
                            </p>
                        </div>
                    </DialogPanel>
                </TransitionChild>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import {
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
} from "@headlessui/vue";

const props = defineProps({
    open: { type: Boolean, required: true },
    orderId: { type: String, default: "" },
    cashier: { type: Object, default: () => ({}) },
    customer: { type: Object, default: () => ({}) },
    products: { type: Array, default: () => [] },
    total: { type: Number, default: 0 },
    paymentMethod: { type: String, default: "Cash" },
});

defineEmits(["update:open"]);

const handlePrint = () => {
    const rows = props.products
        .map((item) => {
            return `
                <tr>
                    <td>${item.name || "-"}</td>
                    <td style="text-align:center;">${Number(item.quantity || 0)}</td>
                    <td style="text-align:right;">${Number(item.selling_price || 0).toFixed(2)}</td>
                </tr>
            `;
        })
        .join("");

    const html = `
        <html>
            <head>
                <title>Return Receipt</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 10px; font-size: 12px; }
                    .title { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 10px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                    th, td { border-bottom: 1px solid #ddd; padding: 6px; }
                    .meta { display: flex; justify-content: space-between; margin: 8px 0; }
                    .total { font-weight: bold; font-size: 14px; margin-top: 12px; display: flex; justify-content: space-between; }
                </style>
            </head>
            <body>
                <div class="title">RETURN RECEIPT</div>
                <div class="meta"><span>Order: ${props.orderId || "-"}</span><span>Date: ${new Date().toLocaleDateString()}</span></div>
                <div class="meta"><span>Customer: ${props.customer?.name || "N/A"}</span><span>Cashier: ${props.cashier?.name || "N/A"}</span></div>
                <div class="meta"><span>Refund Method: ${props.paymentMethod}</span><span></span></div>
                <table>
                    <thead>
                        <tr><th style="text-align:left;">Item</th><th style="text-align:center;">Qty</th><th style="text-align:right;">Unit</th></tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
                <div class="total"><span>Total Refund</span><span>${Number(props.total || 0).toFixed(2)} LKR</span></div>
                <p style="text-align:center;margin-top:10px;">Powered by JAAN Network Ltd.</p>
            </body>
        </html>
    `;

    const printWindow = window.open("", "_blank");
    if (!printWindow) {
        return;
    }

    printWindow.document.open();
    printWindow.document.write(html);
    printWindow.document.close();
    printWindow.onload = () => {
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    };
};
</script>
