<table width="100%" style="text-align: center; margin-bottom: 20px;">
    <tr>
        <td>
            <img src="{{ asset('storage/logo.png') }}" alt="aamarPay Logo" style="max-height: 60px;">
        </td>
    </tr>
</table>

@component('mail::message')
# New Payout Batch Submitted

A new payout batch has been submitted:

- **Batch ID:** {{ $batchId }}
- **Merchant ID:** {{ $merchantId }}
- **Total Amount:** {{ number_format($totalAmount, 2) }} BDT
- **Total Count:** {{ $totalCount }}

Thanks,  
**aamarPay Team**
@endcomponent
