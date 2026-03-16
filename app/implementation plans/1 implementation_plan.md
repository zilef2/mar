# Add PDF Generation to Quotations (Cotización)

This plan details the steps required to add a visually attractive PDF generation feature for the [Cotizacion](file:///c:/laragon/www/mar/app/Models/Cotizacion.php#9-60) module. The PDF will be generated using the `barryvdh/laravel-dompdf` package, allowing us to use Blade templates and CSS for styling.

## Proposed Changes

### Dependencies
Update Composer configurations to include the required PDF package.
#### [MODIFY] [composer.json](file:///c:/laragon/www/mar/composer.json)
- Run `composer require barryvdh/laravel-dompdf` to install the DomPDF wrapper.

---

### Backend Components
Routes and Controller methods to handle the PDF request and render the view.

#### [MODIFY] [web.php](file:///c:/laragon/www/mar/routes/web.php)
- Add a new `GET` route: `Route::get('/Cotizacion/{cotizacion}/pdf', [\App\Http\Controllers\CotizacionController::class, 'generatePdf'])->name('Cotizacion.pdf');`

#### [MODIFY] [CotizacionController.php](file:///c:/laragon/www/mar/app/Http/Controllers/CotizacionController.php)
- Add a new method `generatePdf($id)` that retrieves the [Cotizacion](file:///c:/laragon/www/mar/app/Models/Cotizacion.php#9-60) by ID, loads the Blade view using the DomPDF facade, and returns it as a streamed PDF response.

---

### Views and Styling
A new Blade file dedicated solely to the structure and styling of the PDF output.

#### [NEW] [cotizacion.blade.php](file:///c:/laragon/www/mar/resources/views/pdfs/cotizacion.blade.php)
- Create a complete HTML document with embedded CSS.
- Design choices include modern typography, a clean header (Company/Project details), a well-structured table for financial data (Subtotal, Tax, Total), and a professional footer.
- The design should be very visually attractive out of the box, fulfilling the requirement "muy visualmente atractivo".

---

### Frontend Components
The user interface update to trigger the PDF download.

#### [MODIFY] [Index.vue](file:///c:/laragon/www/mar/resources/js/Pages/Cotizacion/Index.vue)
- Add a new button in the actions column (next to Edit/Delete) using a Document/PDF icon (e.g., `DocumentTextIcon` from Heroicons).
- The button functionality will use `window.open(route('Cotizacion.pdf', id), '_blank')` to open the generated PDF in a new tab for previewing or downloading.

## Verification Plan

### Automated Tests
- *(No automated tests configured for this flow yet.)*

### Manual Verification
1. Open the application locally and navigate to the `Cotizacions` page.
2. Verify that the new PDF button appears for each record.
3. Click the PDF button for a specific record.
4. Confirm that a new tab opens and displays the corresponding PDF.
5. Visually inspect the PDF to ensure the design is clean, professional, and properly displays all quotation data: Engineer Name, Company, Project, Date, Subtotal, Tax, and Total.
