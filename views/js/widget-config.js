document.sezzleConfig = {
    'configGroups': [
        {
            'targetXPath': '.product-prices/.product-price/.current-price/SPAN-0',
            'renderToPath': '..'
        },
        {
            'targetXPath': '.cart-summary-totals/.cart-total/.value',
            'renderToPath': '../../DIV-2',
            'urlMatch': 'cart',
            'alignment': 'right'
        }
    ]
}
