@extends('layouts.app')

@section('content')
<div class="container mx-auto p-8 bg-white rounded">
    <h1 class="text-2xl font-bold mb-6">Apply for Job: <span class="text-gray-700">{{ $job->title }}</span> </h1>
    <form id="job-application-form" method="POST" action="{{ route('job.apply', $job->id) }}" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="block text-gray-700">Resume</label>
            <input type="file" name="resume" id="resume" class="border p-2 rounded w-full" accept="application/pdf"  required>
        </div>
            <!-- Parse Resume Button -->
            <button type="button" id="parse-resume" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 my-2 mb-8 block ms-auto">Parse Resume</button>
            
            <div id="resume-info" class="mt-4">
                <!-- This will display parsed data -->
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <label class="block text-gray-700">Name</label>
            <input type="text" name="name" placeholder="Full Name" class="border p-2 rounded w-full" required>
            <label class="block text-gray-700">Email</label>
            <input type="email" name="email" placeholder="Email Address" class="border p-2 rounded w-full" required>
            <label class="block text-gray-700">Phone</label>
            <input type="tel" name="phone" placeholder="Phone Number" class="border p-2 rounded w-full" required>
            <label class="block text-gray-700">College</label>
            <input type="text" name="college" placeholder="College" class="border p-2 rounded w-full" required>
            <label class="block text-gray-700">Branch</label>
            <input type="text" name="branch" placeholder="Branch" class="border p-2 rounded w-full" required>
            <label class="block text-gray-700">Year Of Passing</label>
            <input type="number" name="year_of_passing" placeholder="Year of Passing" class="border p-2 rounded w-full" required>
            <label class="block text-gray-700">Gender</label>
            <select name="gender" class="border p-2 rounded w-full" required>
                <option value="">Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
            <label class="block text-gray-700">City</label>
            <input type="text" name="current_city" placeholder="Current City" class="border p-2 rounded w-full" required>
            <label class="block text-gray-700">State</label>
            <input type="text" name="state" placeholder="State" class="border p-2 rounded w-full" required>
            <label class="block text-gray-700">Class 10th Percentage</label>
            <input type="number" step="0.01" name="class_10_percentage" placeholder="10th Percentage" class="border p-2 rounded w-full">
            <label class="block text-gray-700">Class 12th Percentage</label>
            <input type="number" step="0.01" name="class_12_percentage" placeholder="12th Percentage" class="border p-2 rounded w-full">
            <label class="block text-gray-700">Graduation Percentage</label>
            <input type="number" step="0.01" name="graduation_percentage" placeholder="Graduation Percentage" class="border p-2 rounded w-full">
            <label class="block text-gray-700">Backlogs (if any)</label>
            <input type="text" name="backlogs" placeholder="Backlogs (if any)" class="border p-2 rounded w-full">
        </div>
        <div class="mt-6">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Submit Application</button>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.15.349/pdf.min.js"></script>
<script src="https://cdnjs.com/libraries/pdf.js"></script>
<script>
document.getElementById('parse-resume').addEventListener('click', function() {
    const fileInput = document.getElementById('resume');
    const file = fileInput.files[0];
    
    if (file && file.type === 'application/pdf') {
        const reader = new FileReader();
        
        reader.onload = function(event) {
            const typedarray = new Uint8Array(event.target.result);
            
            // Using pdf.js to load and parse the PDF
            pdfjsLib.getDocument(typedarray).promise.then(function(pdf) {
                let textContent = '';
                
                // Extract text from all pages in the PDF
                const numPages = pdf.numPages;
                let pagePromises = [];
                
                for (let pageNum = 1; pageNum <= numPages; pageNum++) {
                    pagePromises.push(pdf.getPage(pageNum).then(function(page) {
                        return page.getTextContent().then(function(text) {
                            textContent += text.items.map(item => item.str).join(' ') + '\n';
                        });
                    }));
                }

                // Once all pages are processed, log the content or display it
                Promise.all(pagePromises).then(function() {
                    console.log(textContent);  // Logs the extracted text
                    document.getElementById('resume-info').innerText = textContent;
                });
            }).catch(function(error) {
                console.error('Error extracting PDF content: ', error);
                alert('There was an error parsing the PDF.');
            });
        };

        reader.readAsArrayBuffer(file);  // Read the PDF as an ArrayBuffer
    } else {
        alert('Please upload a valid PDF file.');
    }
});
</script>
@endsection