<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\CustomerDocument;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Picqer\Barcode\BarcodeGeneratorPNG;

class CustomerService
{
    public function __construct(protected CustomerRepositoryInterface $customers)
    {
    }

    /**
     * Create a customer, handle photo upload, and generate QR + barcode images.
     * Wrapped in a transaction since consumer ID generation locks a row.
     *
     * @param  array<string,mixed>  $data
     * @param  UploadedFile[]  $documents
     */
    public function createCustomer(array $data, ?UploadedFile $photo = null, array $documents = []): Customer
    {
        return DB::transaction(function () use ($data, $photo, $documents) {
            if ($photo) {
                $data['photo'] = $photo->store('customers/photos', 'public');
            }

            $data['created_by'] = auth()->id();

            $customer = $this->customers->create($data);

            $this->generateCodes($customer);
            $this->attachDocuments($customer, $documents);

            ActivityLog::record('created', $customer, new: $customer->only(array_keys($data)),
                description: "Customer {$customer->consumer_id} created");

            return $customer->fresh();
        });
    }

    /**
     * @param  array<string,mixed>  $data
     * @param  UploadedFile[]  $documents
     */
    public function updateCustomer(Customer $customer, array $data, ?UploadedFile $photo = null, array $documents = []): Customer
    {
        return DB::transaction(function () use ($customer, $data, $photo, $documents) {
            $old = $customer->only(array_keys($data));

            if ($photo) {
                if ($customer->photo) {
                    Storage::disk('public')->delete($customer->photo);
                }
                $data['photo'] = $photo->store('customers/photos', 'public');
            }

            $customer = $this->customers->update($customer, $data);

            $this->attachDocuments($customer, $documents);

            ActivityLog::record('updated', $customer, old: $old, new: $customer->only(array_keys($data)),
                description: "Customer {$customer->consumer_id} updated");

            return $customer;
        });
    }

    public function deleteCustomer(Customer $customer): bool
    {
        ActivityLog::record('deleted', $customer, description: "Customer {$customer->consumer_id} deleted");

        return $this->customers->delete($customer);
    }

    /**
     * Generate and store a QR code (encodes consumer ID for quick lookup) and
     * a Code128 barcode image, both used on printed bills / ID cards.
     */
    protected function generateCodes(Customer $customer): void
    {
        $qrPath = "customers/qrcodes/{$customer->consumer_id}.svg";
        Storage::disk('public')->put($qrPath, QrCode::format('svg')->size(200)->generate($customer->consumer_id));

        $generator = new BarcodeGeneratorPNG();
        $barcodePath = "customers/barcodes/{$customer->consumer_id}.png";
        Storage::disk('public')->put(
            $barcodePath,
            $generator->getBarcode($customer->barcode ?? $customer->consumer_id, $generator::TYPE_CODE_128)
        );

        $customer->forceFill([
            'qr_code_path' => $qrPath,
        ])->saveQuietly();
    }

    /**
     * @param  UploadedFile[]  $documents
     */
    protected function attachDocuments(Customer $customer, array $documents): void
    {
        foreach ($documents as $document) {
            if (! $document instanceof UploadedFile) {
                continue;
            }

            $path = $document->store('customers/documents', 'public');

            CustomerDocument::create([
                'customer_id' => $customer->id,
                'title' => $document->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $document->getClientMimeType(),
                'file_size' => $document->getSize(),
                'uploaded_by' => auth()->id(),
            ]);
        }
    }
}
