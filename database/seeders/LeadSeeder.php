<?php
namespace Database\Seeders;
use App\Models\Lead;
use Illuminate\Database\Seeder;
class LeadSeeder extends Seeder
{
    public function run(): void
    {
        Lead::create([
            'name' => 'Rahul Kumar',
            'company_name' => 'ABC Technologies Pvt Ltd',
            'email' => 'rahul@gmail.com',
            'phone' => '9876543210',
            'alternate_phone' => '9123456780',
            'source' => 'Website',
            'service' => 'Website Development',
            'status' => 'New',
            'assigned_to' => 1,
            'follow_up_date' => now()->addDays(3),
            'budget' => 50000,
            'notes' => 'Customer needs a business website.',
            'created_by' => 1,
        ]);
        Lead::create([
            'name' => 'Priya Sharma',
            'company_name' => 'Sharma Fashion Store',
            'email' => 'priya@gmail.com',
            'phone' => '9876543211',
            'alternate_phone' => '9123456781',
            'source' => 'Facebook',
            'service' => 'E-commerce Development',
            'status' => 'Contacted',
            'assigned_to' => 1,
            'follow_up_date' => now()->addDays(2),
            'budget' => 80000,
            'notes' => 'Interested in an online fashion store.',
            'created_by' => 1,
        ]);
        Lead::create([
            'name' => 'Amit Singh',
            'company_name' => 'Singh Enterprises',
            'email' => 'amit@gmail.com',
            'phone' => '9876543212',
            'alternate_phone' => null,
            'source' => 'Google',
            'service' => 'Digital Marketing',
            'status' => 'Follow Up',
            'assigned_to' => 1,
            'follow_up_date' => now()->addDays(5),
            'budget' => 35000,
            'notes' => 'Customer wants SEO and Google Ads services.',
            'created_by' => 1,
        ]);
        Lead::create([
            'name' => 'Neha Verma',
            'company_name' => 'NV Solutions',
            'email' => 'neha@gmail.com',
            'phone' => '9876543213',
            'alternate_phone' => '9123456783',
            'source' => 'Reference',
            'service' => 'Mobile App Development',
            'status' => 'Qualified',
            'assigned_to' => 1,
            'follow_up_date' => now()->addDays(7),
            'budget' => 150000,
            'notes' => 'Qualified lead for Android and iOS application.',
            'created_by' => 1,
        ]);
        Lead::create([
            'name' => 'Vikas Gupta',
            'company_name' => 'Gupta Traders',
            'email' => 'vikas@gmail.com',
            'phone' => '9876543214',
            'alternate_phone' => null,
            'source' => 'Call',
            'service' => 'CRM Development',
            'status' => 'Proposal',
            'assigned_to' => 1,
            'follow_up_date' => now()->addDays(4),
            'budget' => 120000,
            'notes' => 'Quotation has been sent to the customer.',
            'created_by' => 1,
        ]);
        Lead::create([
            'name' => 'Sonia Singh',
            'company_name' => 'SS Enterprises',
            'email' => 'sonia@gmail.com',
            'phone' => '9876543215',
            'alternate_phone' => '9123456785',
            'source' => 'Instagram',
            'service' => 'Software Development',
            'status' => 'Won',
            'assigned_to' => 1,
            'follow_up_date' => now()->addDays(10),
            'budget' => 200000,
            'notes' => 'Deal confirmed and project is ready to start.',
            'created_by' => 1,
        ]);
    }
}
