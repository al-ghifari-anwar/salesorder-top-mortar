<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'Dashboard';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// City
$route['city'] = 'City';
$route['insert-city'] = 'City/insert';
$route['update-city/(:num)'] = 'City/update/$1';
$route['delete-city/(:num)'] = 'City/delete/$1';
// Produk
$route['produk'] = 'Produk';
$route['produk/(:num)'] = 'Produk/produk_by_city/$1';
$route['insert-produk'] = 'Produk/insert';
$route['update-produk/(:num)'] = 'Produk/update/$1';
//Auth
$route['login'] = 'Auth';
$route['logout'] = 'Auth/logout';
// Surat Jalan
$route['surat-jalan'] = 'SuratJalan/city_list';
$route['suratjalan/(:num)'] = 'SuratJalan/index/$1';
$route['surat-jalan/(:num)'] = 'SuratJalan/detail/$1';
$route['insert-suratjalan'] = 'SuratJalan/insert';
$route['insert-detsuratjalan'] = 'SuratJalan/insertdetail';
$route['update-detsuratjalan/(:num)'] = 'SuratJalan/updatedetail/$1';
$route['delete-detsuratjalan/(:num)'] = 'SuratJalan/deletedetail/$1';
$route['delete-suratjalan/(:num)'] = 'SuratJalan/delete/$1';
$route['finish-suratjalan/(:num)'] = 'SuratJalan/finish/$1';
$route['print-suratjalan/(:num)'] = 'SuratJalan/print/$1';
$route['sj-not-closing'] = 'SuratJalan/not_closing';
$route['print-tempinv/(:num)'] = 'SuratJalan/print_tempinv/$1';
// Toko
$route['toko'] = 'Toko';
$route['insert-toko'] = 'Toko/insert';
// Kendaraan
$route['kendaraan'] = 'Kendaraan';
$route['insert-kendaraan'] = 'Kendaraan/insert';
$route['update-kendaraan/(:num)'] = 'Kendaraan/update/$1';
$route['delete-kendaraan/(:num)'] = 'Kendaraan/delete/$1';
// Invoice
$route['invoice'] = 'Invoice';
$route['invoice/(:num)'] = 'Invoice/invoice_by_city/$1';
$route['print-invoice/(:num)'] = 'Invoice/print/$1';
$route['invoice-confirm/(:num)'] = 'Invoice/confirm/$1';
$route['invoice/sent'] = 'Invoice/sent';
$route['invoice/sent/(:num)'] = 'Invoice/sent_by_city/$1';
// Rekap Invoice
$route['rekap-city'] = 'Rekap/city_list';
$route['rep-invoice/(:num)'] = 'Rekap/index/$1';
$route['report'] = 'Rekap/rekap';
// Payment
$route['payment'] = 'Payment';
$route['payment-report'] = 'Payment/print';
$route['payment-transit'] = 'Payment/unmatch';
$route['all-payment'] = 'Payment/all';
$route['unassign-payment/(:num)'] = 'Payment/unassign/$1';
$route['update-payment/(:num)'] = 'Payment/update/$1';
$route['remove-payment/(:num)'] = 'Payment/remove/$1';
// Piutang
$route['piutang'] = 'Piutang';
$route['print-piutang'] = 'Piutang/print';
$route['jatuh-tempo'] = 'Piutang/jatuh_tempo';
$route['print-jatuh-tempo'] = 'Piutang/print_jatuh_tempo';
$route['wh-tagihan'] = 'Piutang/webhook_tagihan';
$route['rekap-piutang'] = 'Piutang/rekap';
// Penjualan
$route['penjualan'] = 'Penjualan/city_list';
$route['penjualan/(:num)'] = 'Penjualan/index/$1';
// Visit
$route['visit'] = 'Visit';
$route['visit/(:num)'] = 'Visit/visit_by_city/$1';
$route['approve-visit/(:num)/(:num)'] = 'Visit/approve/$1/$2';
$route['approve-visit2/(:num)/(:num)'] = 'Visit/approve2/$1/$2';
// Rencana Visit
$route['rencana-visit'] = 'Visit/rencana_visit';
$route['rencana-visit/(:num)'] = 'Visit/rencana_visit_by_city/$1';
$route['approve-visit/(:num)/(:num)'] = 'Visit/approve/$1/$2';
// Laporan Kurir
$route['lap-kurir'] = 'Visit/lapkurir_city_list';
$route['lap-kurir/(:num)'] = 'Visit/lapkurir_by_city/$1';
// Voucher
$route['voucher'] = 'Voucher';
$route['reg-voucher/(:num)'] = 'Voucher/regist_voucher/$1';
$route['claim'] = 'Voucher/claim';
$route['claimed'] = 'Voucher/claimed';
$route['voucher-list/(:num)'] = 'Voucher/list_voucher/$1';
$route['lap-voucher/(:num)'] = 'Voucher/laporan_voucher/$1';
$route['vc-manual/(:num)'] = 'Voucher/regist_manual/$1';
$route['vc-penerima/(:num)'] = 'Voucher/laporan_penerima/$1';
// Distributor
$route['distributor'] = 'Distributor';
$route['insert-dist'] = 'Distributor/insert';
$route['update-dist/(:num)'] = 'Distributor/update/$1';
$route['delete-dist/(:num)'] = 'Distributor/delete/$1';
$route['add-dist-user'] = 'Distributor/add_user';
// Sales Rekap
$route['rekap-sales'] = 'Sales';
$route['rekap-sales/(:num)'] = 'Sales/rekap_fee/$1';
$route['print-rekap-sales'] = 'Sales/print_rekap';
// status Rekap
$route['rekap-status'] = 'Status';
$route['rekap-status/(:num)'] = 'Status/rekap_fee/$1';
$route['print-rekap-status'] = 'Status/print_rekap';
// Absen
$route['lap-absen/(:num)/(:any)'] = 'Visit/lap_absen/$1/$2';
$route['lap-absen-renvis/(:num)/(:any)'] = 'Visit/lap_absen_renvis/$1/$2';
// Notif
$route['notif'] = 'Notif/index';
$route['notif-passive'] = 'Notif/notif_passive';
// Surat Jalan
$route['renvis'] = 'Renvis/city_list';
$route['renvis/(:num)'] = 'Renvis/index/$1';
$route['insert-renvis'] = 'Renvis/insert';
// Fee Renvi
$route['feerenvi'] = 'Feerenvi';
$route['feerenvi/(:num)'] = 'Feerenvi/rekap_fee/$1';
$route['feerenvi/print'] = 'Feerenvi/print_rekap';
// Cus Visit
$route['cusvisit'] = 'Cusvisit/city_list';
$route['cusvisit/(:num)'] = 'Cusvisit/index/$1';
$route['cusvisit/print/(:num)'] = 'Cusvisit/print/$1';
// Laporan Auto Transfer Test
$route['autotransfertest'] = 'AutoTransferTest';
$route['autotransfertest/print'] = 'AutoTransferTest/print';
// Voucher Tukang
$route['vctukang'] = 'Vctukang';
$route['vctukang/verify'] = 'Vctukang/verify';
$route['vctukang/toko/(:num)/'] = 'Vctukang/toko/$1/';
$route['vctukang/self'] = 'Vctukang/self';
$route['vctukang/verify/self'] = 'Vctukang/verify_self';
// Manual visit
$route['manualvisit'] = 'Manualvisit';
$route['manualvisit/(:num)'] = 'Manualvisit/city/$1';
$route['manualvisit/insert'] = 'Manualvisit/insert';
// Pirority
$route['priority/(:num)'] = 'Priority/index/$1';
$route['priority/verify/(:num)'] = 'Priority/verify/$1';
// Absen
$route['absen'] = 'Absen';
$route['absen/print'] = 'Absen/print';
// Priority Store
$route['prioritystore'] = 'PriorityStore/index';
// $route['prioritystore/(:num)'] = 'PriorityStore/index/$1';
$route['prioritystore/add'] = 'PriorityStore/add';
$route['prioritystore/delete/(:num)'] = 'PriorityStore/delete/$1';
$route['prioritystore/penukaran/(:num)'] = 'PriorityStore/penukaran/$1';
// Tokopromo Store
$route['tokopromostore'] = 'TokopromoStore/index';
// $route['tokopromostore/(:num)'] = 'TokopromoStore/index/$1';
$route['tokopromostore/add'] = 'TokopromoStore/add';
$route['tokopromostore/delete/(:num)'] = 'TokopromoStore/delete/$1';
$route['tokopromostore/penukaran/(:num)'] = 'TokopromoStore/penukaran/$1';
// Tokopromo
$route['tokopromo/(:num)'] = 'Tokopromo/index/$1';
$route['tokopromo/verify/(:num)'] = 'Tokopromo/verify/$1';
// Proyek
$route['proyek'] = 'Proyek';
$route['insert-proyek'] = 'Proyek/insert';
$route['update-proyek/(:num)'] = 'Proyek/update/$1';
$route['delete-proyek/(:num)'] = 'Proyek/delete/$1';
// Akunseller
$route['akunseller'] = 'Akunseller/index';
$route['akunseller/penukaran'] = 'Akunseller/penukaran';
$route['akunseller/datatukang'] = 'Akunseller/data_tukang';
$route['akunseller/validasi/(:num)'] = 'Akunseller/validasi/$1';
$route['akunseller/deletetukang/(:num)'] = 'Akunseller/delete_tukang/$1';
$route['akunseller/addquota'] = 'Akunseller/add_quota';
$route['akunseller/minquota'] = 'Akunseller/min_quota';
$route['akunseller/quota/(:num)'] = 'Akunseller/quota/$1';
$route['akunseller/penukaran/store/(:num)'] = 'Akunseller/penukaran_store/$1';
$route['akunseller/nonactice/(:num)'] = 'Akunseller/nonactice/$1';
$route['akunseller/active/(:num)'] = 'Akunseller/active/$1';
// $route['akunseller/(:num)'] = 'Akunseller/index/$1';
// Konten
$route['kontenseller'] = 'Konten';
$route['kontenseller/add'] = 'Konten/insert';
$route['kontenseller/update/(:num)'] = 'Konten/update/$1';
$route['kontenseller/delete/(:num)'] = 'Konten/delete/$1';
// Checlist Visit
$route['checklist-visit'] = 'VisitQuestion';
$route['checklist-visit/add'] = 'VisitQuestion/create';
$route['checklist-visit/update/(:num)'] = 'VisitQuestion/update/$1';
$route['checklist-visit/delete/(:num)'] = 'VisitQuestion/destroy/$1';
// Tukang
$route['tukang'] = 'Tukang';
// Sebar Voucher Tukang
$route['sebarvctukang'] = 'Tukang/sebar_vc_city';
$route['sebarvctukang/(:num)'] = 'Tukang/sebar_vc/$1';
$route['sebarvctukang/create/(:num)'] = 'Tukang/create_vc/$1';
// Penukaran Store
$route['penukaranstore'] = 'Penukaranstore/index';
$route['penukaranstore/add_latlong'] = 'Penukaranstore/add_latlong';
$route['penukaranstore/list'] = 'Penukaranstore/list';
// $route['penukaranstore/(:num)'] = 'Penukaranstore/index/$1';
// Company
$route['company'] = 'Company';
$route['company/update/(:num)'] = 'Company/update/$1';
// Company
$route['qontak'] = 'Qontak';
$route['qontak/update/(:num)'] = 'Qontak/update/$1';
// Renvi MG
$route['renvimg'] = 'Renvimg/city_list';
$route['renvimg/(:num)'] = 'Renvimg/index/$1';
$route['renvimg/insert'] = 'Renvimg/insert';
// Analisa
$route['analisa/passive'] = 'Analisa/passive';
$route['analisa/order5'] = 'Analisa/passive_order_5';
$route['analisa/active'] = 'Analisa/active';
// Referal
$route['referal/(:any)'] = 'Referal/index/$1';
$route['referal/verify/(:any)'] = 'Referal/verify/$1';
$route['referal/complete/verify'] = 'Referal/complete_verify';
// Trial Watzap
$route['watzap/trial/tukang'] = 'Watzap/sendTrialTukang';
$route['watzap/insert/tukang'] = 'Watzap/insertTukangToWaitlist';
// Blast Konten
$route['marketing/tukang'] = 'Marketing/tukang';
$route['marketing/rekap/tukang'] = 'Marketing/rekap_tukang';
$route['marketing'] = 'Marketing';
$route['insert-marketing'] = 'Marketing/insert';
$route['delete-marketing/(:num)/(:any)'] = 'Marketing/delete/$1/$2';
$route['update-marketing/(:num)'] = 'Marketing/update/$1';
// Gudang
$route['gudang'] = 'Gudang';
$route['gudang/create'] = 'Gudang/create';
$route['gudang/update/(:num)'] = 'Gudang/update/$1';
$route['gudang/delete/(:num)'] = 'Gudang/delete/$1';
// Stok Produk
$route['insert-stok/(:num)'] = 'Produk/insert_stok/$1';
$route['move-stok/(:num)'] = 'Produk/move_stok/$1';
$route['produk/(:num)/(:num)'] = 'Produk/stok_by_produk/$1/$2';
$route['delete-stok/(:num)/(:num)/(:num)'] = 'Produk/delete_stok/$1/$2/$3';
// Stok
$route['stok'] = 'Stok';
$route['stok/sync'] = 'Stok/sync_stok';
$route['stok/(:num)'] = 'Stok/list/$1';
$route['cetak-stok'] = 'Stok/lap_stok';
// Tambah Stok / Sjstok
$route['sjstok'] = 'Sjstok';
$route['sjstok/create'] = 'Sjstok/create';
$route['sjstok/update/(:num)'] = 'Sjstok/update/$1';
$route['sjstok/delete/(:num)'] = 'Sjstok/delete/$1';
$route['sjstok/print/(:num)'] = 'Sjstok/print/$1';
$route['sjstok/(:num)'] = 'Sjstok/detail/$1';
$route['sjstok/detail/create'] = 'Sjstok/createDetail';
$route['sjstok/detail/update/(:num)'] = 'Sjstok/updateDetail/$1';
$route['sjstok/detail/delete/(:num)'] = 'Sjstok/deleteDetail/$1';
$route['sjstok/rechieved/(:num)'] = 'Sjstok/rechieved/$1';
$route['sjstok/rechieved/update/(:num)'] = 'Sjstok/updateRechieved/$1';
$route['sjstok/rechieved/save/(:num)'] = 'Sjstok/rechieved_save/$1';
// Adjustment Stok
$route['sjstok/adjustment'] = 'Sjstok/adjustment';
$route['sjstok/adjustment/create'] = 'Sjstok/adjustmentCreate';
$route['sjstok/adjustment/delete/(:num)'] = 'Sjstok/adjustmentDelete/$1';
// Gudang
$route['masterproduk'] = 'Masterproduk';
$route['masterproduk/sync'] = 'Masterproduk/sync';
$route['masterproduk/create'] = 'Masterproduk/create';
$route['masterproduk/update/(:num)'] = 'Masterproduk/update/$1';
$route['masterproduk/delete/(:num)'] = 'Masterproduk/delete/$1';
// Analisa Tukang
$route['analisatukang/user'] = 'Analisatukang/user';
$route['analisatukang/tampil/(:num)'] = 'Analisatukang/tampil/$1';
$route['analisatukang/matikan/(:num)'] = 'Analisatukang/matikan/$1';
$route['analisatukang/laporan'] = 'Analisatukang/laporan';
$route['analisatukang/laporan/print'] = 'Analisatukang/laporan_print';
// Maxchat
$route['maxchat/inbound'] = 'Maxchat/inbound';
$route['maxchat/outbound'] = 'Maxchat/outbound';
// Scoring
$route['scoring'] = 'Scoring/city_list';
$route['scoring/(:num)'] = 'Scoring/list/$1';
$route['scoring/rekap/(:num)'] = 'Scoring/rekap/$1';
// Log bca
$route['logbca'] = 'Logbca';
// Top Event
$route['topevent'] = 'Topevent';
// Tagihan
$route['tagihan/create'] = 'Tagihan/createTagihan';
$route['tagihan'] = 'Tagihan/index';
$route['tagihan/detail/(:num)'] = 'Tagihan/detail/$1';
$route['tagihan/print/(:num)'] = 'Tagihan/print/$1';
// Runcron
$route['runcron/stmt'] = 'Runcron/stmt';
// Notif invoice
$route['notif/invoice'] = 'Notif/send_invoice';
$route['notif/invoice/backup'] = 'Notif/send_invoice_backup';
// Invoice Change
$route['invoice/change/(:num)'] = 'Invoice/changeTermin/$1';
// Combine Scoring
$route['scoring/combine/(:num)'] = 'Scoring/combineScoring/$1';
$route['scoring/setbad'] = 'Scoring/setBadScore';
// Bad Score
$route['badscore'] = 'Badscore/city_list';
$route['badscore/(:num)'] = 'Badscore/list/$1';
$route['badscore/approve/(:num)'] = 'Badscore/approve/$1';
$route['badscore/tampilkan/(:num)'] = 'Badscore/tampilkan/$1';
// Minimum stok
$route['minimumstok/(:num)'] = 'Minimumstok/index/$1';
$route['minimumstok/create'] = 'Minimumstok/create';
$route['minimumstok/update'] = 'Minimumstok/update';
$route['minimumstok/delete/(:num)'] = 'Minimumstok/delete/$1';
// Invoice COD
$route['invoicecod/waiting'] = 'Invoicecod/waiting';
// $route['invoicecod/waiting/(:num)'] = 'Invoicecod/waiting/$1';
// Apporder
$route['apporder'] = 'Apporder/index';
$route['apporder/approve/(:num)'] = 'Apporder/approve/$1';
// Topseller
$route['topseller/setting/global'] = 'TopsellerSetting/index';
$route['topseller/setting/global/update'] = 'TopsellerSetting/update';
$route['topseller/setting/discount'] = 'TopsellerDiscount/index';
$route['topseller/setting/discount/update'] = 'TopsellerDiscount/update';
