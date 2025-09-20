<?php

namespace Database\Seeders;

use App\Models\Form;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            $forms = [
            [
                'id' => 1,
                'act' => 'kyc',
                'form_data' => [
                    'full_name' => [
                        'name' => 'Full Name',
                        'label' => 'full_name',
                        'is_required' => 'required',
                        'instruction' => null,
                        'extensions' => null,
                        'options' => [],
                        'type' => 'text',
                        'width' => '6',
                    ],
                    'id_no' => [
                        'name' => 'NIC Number',
                        'label' => 'id_no',
                        'is_required' => 'required',
                        'instruction' => null,
                        'extensions' => null,
                        'options' => [],
                        'type' => 'text',
                        'width' => '6',
                    ],
                    'gender' => [
                        'name' => 'Gender',
                        'label' => 'gender',
                        'is_required' => 'required',
                        'instruction' => null,
                        'extensions' => null,
                        'options' => ['Male', 'Female', 'Other'],
                        'type' => 'radio',
                        'width' => '6',
                    ],
                    'religion' => [
                        'name' => 'religion',
                        'label' => 'religion',
                        'is_required' => 'required',
                        'instruction' => 'To bring you the most relevant festival offers, please provide your religion or ethnicity.',
                        'extensions' => null,
                        'options' => ['Sinhala', 'Muslims', 'Tamil', 'Christian/Catholic'],
                        'type' => 'select',
                        'width' => '6',
                    ],
                    'address' => [
                        'name' => 'Address',
                        'label' => 'address',
                        'is_required' => 'required',
                        'instruction' => null,
                        'extensions' => null,
                        'options' => [],
                        'type' => 'text',
                        'width' => '12',
                    ],
                    'city' => [
                        'name' => 'City',
                        'label' => 'city',
                        'is_required' => 'required',
                        'instruction' => null,
                        'extensions' => null,
                        'options' => [],
                        'type' => 'text',
                        'width' => '4',
                    ],
                    'state' => [
                        'name' => 'State',
                        'label' => 'state',
                        'is_required' => 'required',
                        'instruction' => null,
                        'extensions' => null,
                        'options' => [],
                        'type' => 'text',
                        'width' => '4',
                    ],
                    'zip_code' => [
                        'name' => 'Zip Code',
                        'label' => 'zip_code',
                        'is_required' => 'required',
                        'instruction' => null,
                        'extensions' => null,
                        'options' => [],
                        'type' => 'text',
                        'width' => '4',
                    ],
                    'identity_document_(frontside)' => [
                        'name' => 'Identity Document (Frontside)',
                        'label' => 'identity_document_(frontside)',
                        'is_required' => 'required',
                        'instruction' => null,
                        'extensions' => 'jpg,jpeg,png,pdf,doc,docx,txt,xlx,xlsx,csv',
                        'options' => [],
                        'type' => 'file',
                        'width' => '12',
                    ],
                    'identity_document_(backside)' => [
                        'name' => 'Identity Document (Backside)',
                        'label' => 'identity_document_(backside)',
                        'is_required' => 'required',
                        'instruction' => null,
                        'extensions' => 'jpg,jpeg,png,pdf,doc,docx,txt,xlx,xlsx,csv',
                        'options' => [],
                        'type' => 'file',
                        'width' => '12',
                    ],
                ],
            ],
            [
                'id' => 2,
                'act' => 'manual_deposit',
                'form_data' => [
                    'payment_proof' => [
                        'name' => 'Payment Proof',
                        'label' => 'payment_proof',
                        'is_required' => 'required',
                        'instruction' => null,
                        'extensions' => 'jpg,jpeg,png,pdf',
                        'options' => [],
                        'type' => 'file',
                        'width' => '6',
                    ],
                    'slip_number' => [
                        'name' => 'Transaction ID',
                        'label' => 'transaction_id',
                        'is_required' => 'required',
                        'instruction' => 'Enter Your Transaction ID',
                        'extensions' => null,
                        'options' => [],
                        'type' => 'text',
                        'width' => '6',
                    ],
                    // 'payment_proof' => [
                    //     'name' => 'Slip Number',
                    //     'label' => 'slip_number',
                    //     'is_required' => 'required',
                    //     'instruction' => 'Enter Your Slip Number',
                    //     'extensions' => null,
                    //     'options' => [],
                    //     'type' => 'text',
                    //     'width' => '6',
                    // ],
                    // 'payment_proof' => [
                    //     'name' => 'Slip Number',
                    //     'label' => 'slip_number',
                    //     'is_required' => 'required',
                    //     'instruction' => 'Enter Your Slip Number',
                    //     'extensions' => null,
                    //     'options' => [],
                    //     'type' => 'text',
                    //     'width' => '6',
                    // ],
                    // 'payment_proof' => [
                    //     'name' => 'Slip Number',
                    //     'label' => 'slip_number',
                    //     'is_required' => 'required',
                    //     'instruction' => 'Enter Your Slip Number',
                    //     'extensions' => null,
                    //     'options' => [],
                    //     'type' => 'text',
                    //     'width' => '6',
                    // ],
                    // 'payment_proof' => [
                    //     'name' => 'Slip Number',
                    //     'label' => 'slip_number',
                    //     'is_required' => 'required',
                    //     'instruction' => 'Enter Your Slip Number',
                    //     'extensions' => null,
                    //     'options' => [],
                    //     'type' => 'text',
                    //     'width' => '6',
                    // ],
                ],
            ],
            [
                'id' => 3,
                'act' => 'manual_deposit',
                'form_data' => [
                    'payment_proof' => [
                        'name' => 'Payment Proof',
                        'label' => 'payment_proof',
                        'is_required' => 'required',
                        'instruction' => null,
                        'extensions' => 'jpg,jpeg,png,pdf',
                        'options' => [],
                        'type' => 'file',
                        'width' => '12',
                    ],
                ],
            ],
            [
                'id' => 4,
                'act' => 'withdraw_method',
                'form_data' => [
                    'bank_name' => [
                        'name' => 'Bank Name',
                        'label' => 'bank_name',
                        'is_required' => 'required',
                        'instruction' => null,
                        'extensions' => '',
                        'options' => [],
                        'type' => 'text',
                        'width' => '12',
                    ],
                    'account_number' => [
                        'name' => 'Account Number',
                        'label' => 'account_number',
                        'is_required' => 'required',
                        'instruction' => null,
                        'extensions' => '',
                        'options' => [],
                        'type' => 'text',
                        'width' => '12',
                    ],
                    'full_name' => [
                        'name' => 'Full Name',
                        'label' => 'full_name',
                        'is_required' => 'required',
                        'instruction' => null,
                        'extensions' => '',
                        'options' => [],
                        'type' => 'text',
                        'width' => '12',
                    ],
                    'branch' => [
                        'name' => 'Branch',
                        'label' => 'branch',
                        'is_required' => 'required',
                        'instruction' => null,
                        'extensions' => '',
                        'options' => [],
                        'type' => 'text',
                        'width' => '12',
                    ],
                ]
            ],
            [
                'id' => 5,
                'act' => 'withdraw_method',
                'form_data' => []
            ],
        ];

        foreach ($forms as $form) {
            Form::create($form);
        }
    }
}
