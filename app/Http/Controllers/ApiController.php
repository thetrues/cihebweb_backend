<?php

namespace App\Http\Controllers;

use App\Models\AboutUs;
use App\Models\Gallery;
use App\Models\Initiative;
use App\Models\Portfolio;
use App\Models\Project;
use App\Models\Slider;
use App\Models\SuccessStory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function getData()
    {
        $sliders = Slider::where('is_active', true)
            ->orderBy('display_order', 'asc')
            ->get();
        $aboutUs = AboutUs::first();
        $initiatives = Initiative::all();
        $gallery = Gallery::all();
        $projects = Project::all();
        $portfolio = Portfolio::all();
        return response()->json([
            'sliders' => $sliders,
            'about_us' => $aboutUs,
            'initiatives' => $initiatives,
            'gallery' => $gallery,
            'projects' => $projects,
            'portfolio' => $portfolio,
        ]);
    }

    public function getSliders()
    {
        $sliders = Slider::where('is_active', true)
            ->orderBy('display_order', 'asc')
            ->get();
        return response()->json($sliders);
    }

    public function getAboutUs()
    {
        $aboutUs = AboutUs::first();
        return response()->json($aboutUs);
    }

    public function getInitiatives()
    {
        $initiatives = Initiative::all();
        return response()->json($initiatives);
    }

    public function getGallery()
    {
        $gallery = Gallery::all();
        return response()->json($gallery);
    }

    public function getProjects()
    {
        $projects = Project::all();
        return response()->json($projects);
    }   

    public function getPortfolio()
    {
        $portfolio = Portfolio::all();
        return response()->json($portfolio);
    }   

    public function getProjectById($id)
    {
        $project = Project::find($id);
        if ($project) {
            return response()->json($project);
        } else {
            return response()->json(['message' => 'Project not found'], 404);
        }
    }   

    public function getInitiativeById($id)
    {
        $initiative = Initiative::find($id);
        if ($initiative) {
            return response()->json($initiative);
        } else {
            return response()->json(['message' => 'Initiative not found'], 404);
        }
    }   

    public function getPortfolioById($id)
    {
        $portfolio = Portfolio::find($id);
        if ($portfolio) {
            return response()->json($portfolio);
        } else {
            return response()->json(['message' => 'Portfolio item not found'], 404);
        }
    }

    public function getGalleryById($id)
    {
        $galleryItem = Gallery::find($id);
        if ($galleryItem) {
            return response()->json($galleryItem);
        } else {
            return response()->json(['message' => 'Gallery item not found'], 404);
        }
    }

    public function getSliderById($id)
    {
        $slider = Slider::find($id);
        if ($slider) {
            return response()->json($slider);
        } else {
            return response()->json(['message' => 'Slider not found'], 404);
        }
    }

    public function getAboutUsById($id)
    {
        $aboutUs = AboutUs::find($id);
        if ($aboutUs) {
            return response()->json($aboutUs);
        } else {
            return response()->json(['message' => 'About Us entry not found'], 404);
        }
    }

    public function createSlider(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image_file' => 'required|file|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        //move file from storage/app to storage/app/public/sliders
        // Store initially on local disk (storage/app)
        $localPath = $request->file('image_file')->store('sliders');

        // Move to public disk (storage/app/public/sliders)
        $filename = basename($localPath);
       Storage::disk('public')->makeDirectory('sliders');
        $contents = Storage::disk('local')->get($localPath);
        Storage::disk('public')->put("sliders/{$filename}", $contents);
        Storage::disk('local')->delete($localPath);

        // Persist relative path for public disk
        $validated['image_path'] = "sliders/{$filename}";

        $slider = Slider::create($validated);

        return response()->json(['message' => 'Slider created successfully', 'slider' => $slider], 201);
    }


    public function deactivateSlider($id){
        $slider = Slider::find($id);
        if (!$slider) {
            return response()->json(['message' => 'Slider not found'], 404);
        }

        $slider->is_active = false;
        $slider->save();

        return response()->json(['message' => 'Slider deactivated successfully', 'slider' => $slider], 200);
    }


    public function activateSlider($id){
        $slider = Slider::find($id);
        if (!$slider) {
            return response()->json(['message' => 'Slider not found'], 404);
        }

        $slider->is_active = true;
        $slider->save();

        return response()->json(['message' => 'Slider activated successfully', 'slider' => $slider], 200);
    }

    public function deleteSlider($id){
        $slider = Slider::find($id);
        if (!$slider) {
            return response()->json(['message' => 'Slider not found'], 404);
        }

        // Optionally delete the image file from storage
        if ($slider->image_path && Storage::disk('public')->exists($slider->image_path)) {
            Storage::disk('public')->delete($slider->image_path);
        }

        $slider->delete();

        return response()->json(['message' => 'Slider deleted successfully'], 200);
    }

   
    public function createAboutUs(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'required|string',
            'mission' => 'required|string|max:255',
            'vision' => 'required|string|max:255',
            'values' => 'required|string|max:255',
            'established_year' => 'required|integer',
            'team_members' => 'required|string|max:255',
            'regions' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:255',
            'social_media_links' => 'nullable|string|max:255',
            'awards' => 'nullable|string|max:255',
            'founder' => 'nullable|string|max:255',
            'location' => 'required|string|max:255',
            'image_file' => 'nullable|file|image|max:2048',
            'video_file' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // Handle image file upload
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('about_us', 'public');
            $validated['image_path'] = $imagePath;
        }

        // Handle video file upload
        if ($request->hasFile('video_file')) {
            $videoPath = $request->file('video_file')->store('about_us', 'public');
            $validated['video_path'] = $videoPath;
        }

        $aboutUs = AboutUs::create($validated);

        return response()->json(['message' => 'About Us entry created successfully', 'about_us' => $aboutUs], 201);
    }


    public function deleteAboutUs($id){
        $aboutUs = AboutUs::find($id);
        if (!$aboutUs) {
            return response()->json(['message' => 'About Us entry not found'], 404);
        }

        // Optionally delete the image and video files from storage
        if ($aboutUs->image_path && Storage::disk('public')->exists($aboutUs->image_path)) {
            Storage::disk('public')->delete($aboutUs->image_path);
        }
        if ($aboutUs->video_path && Storage::disk('public')->exists($aboutUs->video_path)) {
            Storage::disk('public')->delete($aboutUs->video_path);
        }

        $aboutUs->delete();

        return response()->json(['message' => 'About Us entry deleted successfully'], 200);
    }

    public function updateAboutUs(Request $request, $id){
        $aboutUs = AboutUs::find($id);
        if (!$aboutUs) {
            return response()->json(['message' => 'About Us entry not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'subtitle' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|required|string',
            'mission' => 'sometimes|required|string|max:255',
            'vision' => 'sometimes|required|string|max:255',
            'values' => 'sometimes|required|string|max:255',
            'established_year' => 'sometimes|required|integer',
            'team_members' => 'sometimes|required|string|max:255',
            'regions' => 'sometimes|required|string|max:255',
            'contact_email' => 'sometimes|required|email|max:255',
            'contact_phone' => 'sometimes|required|string|max:255',
            'social_media_links' => 'sometimes|nullable|string|max:255',
            'awards' => 'sometimes|nullable|string|max:255',
            'founder' => 'sometimes|nullable|string|max:255',
            'location' => 'sometimes|required|string|max:255',
            'image_file' => 'sometimes|nullable|file|image|max:2048',
            'video_file' => 'sometimes|nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // Handle image file upload
        if ($request->hasFile('image_file')) {
            // Delete old image if exists
            if ($aboutUs->image_path && Storage::disk('public')->exists($aboutUs->image_path)) {
                Storage::disk('public')->delete($aboutUs->image_path);
            }
            $imagePath = $request->file('image_file')->store('about_us', 'public');
            $validated['image_path'] = $imagePath;
        }

        // Handle video file upload
        if ($request->hasFile('video_file')) {
            // Delete old video if exists
            if ($aboutUs->video_path && Storage::disk('public')->exists($aboutUs->video_path)) {
                Storage::disk('public')->delete($aboutUs->video_path);
            }
            $videoPath = $request->file('video_file')->store('about_us', 'public');
            $validated['video_path'] = $videoPath;
        }

        $aboutUs->update($validated);

        return response()->json(['message' => 'About Us entry updated successfully', 'about_us' => $aboutUs], 200);
    }


    public function listAboutUsEntries()
    {
        $aboutUsEntries = AboutUs::all();
        return response()->json($aboutUsEntries);
    }

    public function getLatestAboutUsEntry()
    {
        $aboutUs = AboutUs::latest()->first();
        return response()->json($aboutUs);
    }


    public function createInitiative(Request $request){
       $validator= Validator::make($request->all(), [
           'title' => 'required|string|max:255',
           'subtitle' => 'nullable|string|max:255',
           'image_file' => 'nullable|file|image|max:2048',
           'video_file' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:10240',
           'short_description' => 'nullable|string',
           'detailed_description' => 'nullable|string',
       ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Handle image file upload
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('initiatives', 'public');
            $validated['image_path'] = $imagePath;
        }

        // Handle video file upload
        if ($request->hasFile('video_file')) {
            $videoPath = $request->file('video_file')->store('initiatives', 'public');
            $validated['video_path'] = $videoPath;
        }   

        $validated = $validator->validated();

        $initiative = Initiative::create($validated);
        return response()->json(['message' => 'Initiative created successfully', 'initiative' => $initiative], 201);
    }


    public function listInitiatives()
    {
        $initiatives = Initiative::all();
        return response()->json($initiatives);
    }

    public function updateInitiative(Request $request, $id){
        $initiative = Initiative::find($id);
        if (!$initiative) {
            return response()->json(['message' => 'Initiative not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'subtitle' => 'sometimes|nullable|string|max:255',
            'image_file' => 'sometimes|nullable|file|image|max:2048',
            'video_file' => 'sometimes|nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:10240',
            'short_description' => 'sometimes|nullable|string',
            'detailed_description' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // Handle image file upload
        if ($request->hasFile('image_file')) {
            // Delete old image if exists
            if ($initiative->image_path && Storage::disk('public')->exists($initiative->image_path)) {
                Storage::disk('public')->delete($initiative->image_path);
            }
            $imagePath = $request->file('image_file')->store('initiatives', 'public');
            $validated['image_path'] = $imagePath;
        }

        // Handle video file upload
        if ($request->hasFile('video_file')) {
            // Delete old video if exists
            if ($initiative->video_path && Storage::disk('public')->exists($initiative->video_path)) {
                Storage::disk('public')->delete($initiative->video_path);
            }
            $videoPath = $request->file('video_file')->store('initiatives', 'public');
            $validated['video_path'] = $videoPath;
        }

        $initiative->update($validated);

        return response()->json(['message' => 'Initiative updated successfully', 'initiative' => $initiative], 200);
    }

    public function deleteInitiative($id){
        $initiative = Initiative::find($id);
        if (!$initiative) {
            return response()->json(['message' => 'Initiative not found'], 404);
        }

        // Optionally delete the image and video files from storage
        if ($initiative->image_path && Storage::disk('public')->exists($initiative->image_path)) {
            Storage::disk('public')->delete($initiative->image_path);
        }
        if ($initiative->video_path && Storage::disk('public')->exists($initiative->video_path)) {
            Storage::disk('public')->delete($initiative->video_path);
        }

        $initiative->delete();

        return response()->json(['message' => 'Initiative deleted successfully'], 200);
    }

    public function createProject(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image_file' => 'nullable|file|image|max:2048',
            'video_file' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:10240',
            'short_description' => 'nullable|string',
            'detailed_description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'partner_organizations' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'funding_source' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric',
            'status' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // Handle image file upload
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('projects', 'public');
            $validated['image_path'] = $imagePath;
        }

        // Handle video file upload
        if ($request->hasFile('video_file')) {
            $videoPath = $request->file('video_file')->store('projects', 'public');
            $validated['video_path'] = $videoPath;
        }

        $project = Project::create($validated);

        return response()->json(['message' => 'Project created successfully', 'project' => $project], 201);
    }

    public function listProjects()
    {
        $projects = Project::all();
        return response()->json($projects);
    }

    public function deleteProject($id){
        $project = Project::find($id);
        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        // Optionally delete the image and video files from storage
        if ($project->image_path && Storage::disk('public')->exists($project->image_path)) {
            Storage::disk('public')->delete($project->image_path);
        }
        if ($project->video_path && Storage::disk('public')->exists($project->video_path)) {
            Storage::disk('public')->delete($project->video_path);
        }

        $project->delete();

        return response()->json(['message' => 'Project deleted successfully'], 200);
    }

    public function updateProject(Request $request, $id){
        $project = Project::find($id);
        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'subtitle' => 'sometimes|nullable|string|max:255',
            'image_file' => 'sometimes|nullable|file|image|max:2048',
            'video_file' => 'sometimes|nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:10240',
            'short_description' => 'sometimes|nullable|string',
            'detailed_description' => 'sometimes|nullable|string',
            'location' => 'sometimes|nullable|string|max:255',
            'partner_organizations' => 'sometimes|nullable|string|max:255',
            'start_date' => 'sometimes|nullable|date',
            'end_date' => 'sometimes|nullable|date',
            'funding_source' => 'sometimes|nullable|string|max:255',
            'budget' => 'sometimes|nullable|numeric',
            'status' => 'sometimes|nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // Handle image file upload
        if ($request->hasFile('image_file')) {
            // Delete old image if exists
            if ($project->image_path && Storage::disk('public')->exists($project->image_path)) {
                Storage::disk('public')->delete($project->image_path);
            }
            $imagePath = $request->file('image_file')->store('projects', 'public');
            $validated['image_path'] = $imagePath;
        }

        // Handle video file upload
        if ($request->hasFile('video_file')) {
            // Delete old video if exists
            if ($project->video_path && Storage::disk('public')->exists($project->video_path)) {
                Storage::disk('public')->delete($project->video_path);
            }
            $videoPath = $request->file('video_file')->store('projects', 'public');
            $validated['video_path'] = $videoPath;
        }
        $project->update($validated);
        return response()->json(['message' => 'Project updated successfully', 'project' => $project], 200);
    }

    public function createPortfolio(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image_file' => 'nullable|file|image|max:2048',
            'video_file' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:10240',
            'file_file' => 'nullable|file|max:10240',
            'short_description' => 'nullable|string',
            'detailed_description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // Handle image file upload
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('portfolio', 'public');
            $validated['image_path'] = $imagePath;
        }

        // Handle video file upload
        if ($request->hasFile('video_file')) {
            $videoPath = $request->file('video_file')->store('portfolio', 'public');
            $validated['video_path'] = $videoPath;
        }

        // Handle general file upload
        if ($request->hasFile('file_file')) {
            $filePath = $request->file('file_file')->store('portfolio', 'public');
            $validated['file_path'] = $filePath;
        }

        $portfolio = Portfolio::create($validated);

        return response()->json(['message' => 'Portfolio item created successfully', 'portfolio' => $portfolio], 201);
    }


    public function createGallery(Request $request){
             $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image_file' => 'required',
            'video_file' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:10240',
            'file_file' => 'nullable|file|max:10240',
            'short_description' => 'nullable|string',
            'detailed_description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // Handle image file upload base64
         $imagePath = null;
            
        if ($request->has('image_file')) {
            $imageData = $request->input('image_file');
            // Remove data URI prefix if present
            if (strpos($imageData, 'data:') === 0) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
            }
            $decodedImage = base64_decode($imageData);
            $mimeType = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $decodedImage);
            $extension = match($mimeType) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                default => 'jpg',
            };
            $imageName = time() . '.' . $extension;
            $imagePath = 'gallery/' . $imageName;
            Storage::disk('public')->put($imagePath, $decodedImage);
            $request->merge(['image_file' => $imagePath]);
            $request->request->set('image_file', $imagePath);
            $validated['image_path'] = $imagePath;
        }
        

           // Handle video file upload
        if ($request->hasFile('video_file')) {
            $videoPath = $request->file('video_file')->store('gallery', 'public');
            $validated['video_path'] = $videoPath;
        }

        // Handle general file upload
        if ($request->hasFile('file_file')) {
            $filePath = $request->file('file_file')->store('gallery', 'public');
            $validated['file_path'] = $filePath;
        }

        $gallery = Gallery::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'image_path' => $validated['image_path'] ?? null,
            'video_path' => $validated['video_path'] ?? null,
            'file_path' => $validated['file_path'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'detailed_description' => $validated['detailed_description'] ?? null,
        ]);

        return response()->json(['message' => 'Gallery uploaded successfully', 'gallery' => $gallery], 201);

    }


    public function updateGallery(Request $request, $id){
        $gallery = Gallery::find($id);
        if (!$gallery) {
            return response()->json(['message' => 'Gallery item not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'subtitle' => 'sometimes|nullable|string|max:255',
            'image_file' => 'sometimes|nullable',
            'video_file' => 'sometimes|nullable|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:10240',
            'file_file' => 'sometimes|nullable|max:10240',
            'short_description' => 'sometimes|nullable|string',
            'detailed_description' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

         $imagePath = null;
            
        if ($request->has('image_file')) {
            $imageData = $request->input('image_file');
            // Remove data URI prefix if present
            if (strpos($imageData, 'data:') === 0) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
            }
            $decodedImage = base64_decode($imageData);
            $mimeType = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $decodedImage);
            $extension = match($mimeType) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                default => 'jpg',
            };
            $imageName = time() . '.' . $extension;
            $imagePath = 'gallery/' . $imageName;
            Storage::disk('public')->put($imagePath, $decodedImage);
            $request->merge(['image_file' => $imagePath]);
            $request->request->set('image_file', $imagePath);
            $validated['image_path'] = $imagePath;
        }
        


        // Handle video file upload
        if ($request->hasFile('video_file')) {
            // Delete old video if exists
            if ($gallery->video_path && Storage::disk('public')->exists($gallery->video_path)) {
                Storage::disk('public')->delete($gallery->video_path);
            }
            $videoPath = $request->file('video_file')->store('gallery', 'public');
            $validated['video_path'] = $videoPath;
        }

        // Handle general file upload
        if ($request->hasFile('file_file')) {
            // Delete old file if exists
            if ($gallery->file_path && Storage::disk('public')->exists($gallery->file_path)) {
                Storage::disk('public')->delete($gallery->file_path);
            }
            $filePath = $request->file('file_file')->store('gallery', 'public');
            $validated['file_path'] = $filePath;
        }
        $gallery->update($validated);

        return response()->json(['message' => 'Gallery updated successfully', 'gallery' => $gallery], 200);
    }

    public function listPortfolio()
    {
        $portfolio = Portfolio::all();
        return response()->json($portfolio);
    }

    public function deletePortfolio($id){
        $portfolio = Portfolio::find($id);
        if (!$portfolio) {
            return response()->json(['message' => 'Portfolio item not found'], 404);
        }

        // Optionally delete the image, video, and file from storage
        if ($portfolio->image_path && Storage::disk('public')->exists($portfolio->image_path)) {
            Storage::disk('public')->delete($portfolio->image_path);
        }
        if ($portfolio->video_path && Storage::disk('public')->exists($portfolio->video_path)) {
            Storage::disk('public')->delete($portfolio->video_path);
        }
        if ($portfolio->file_path && Storage::disk('public')->exists($portfolio->file_path)) {
            Storage::disk('public')->delete($portfolio->file_path);
        }

        $portfolio->delete();

        return response()->json(['message' => 'Portfolio item deleted successfully'], 200);
    }


    public function updatePortfolio(Request $request, $id){
        $portfolio = Portfolio::find($id);
        if (!$portfolio) {
            return response()->json(['message' => 'Portfolio item not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'subtitle' => 'sometimes|nullable|string|max:255',
            'image_file' => 'sometimes|nullable|file|image|max:2048',
            'video_file' => 'sometimes|nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:10240',
            'file_file' => 'sometimes|nullable|file|max:10240',
            'short_description' => 'sometimes|nullable|string',
            'detailed_description' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // Handle image file upload
        if ($request->hasFile('image_file')) {
            // Delete old image if exists
            if ($portfolio->image_path && Storage::disk('public')->exists($portfolio->image_path)) {
                Storage::disk('public')->delete($portfolio->image_path);
            }
            $imagePath = $request->file('image_file')->store('portfolio', 'public');
            $validated['image_path'] = $imagePath;
        }

        // Handle video file upload
        if ($request->hasFile('video_file')) {
            // Delete old video if exists
            if ($portfolio->video_path && Storage::disk('public')->exists($portfolio->video_path)) {
                Storage::disk('public')->delete($portfolio->video_path);
            }
            $videoPath = $request->file('video_file')->store('portfolio', 'public');
            $validated['video_path'] = $videoPath;
        }

        // Handle general file upload
        if ($request->hasFile('file_file')) {
            // Delete old file if exists
            if ($portfolio->file_path && Storage::disk('public')->exists($portfolio->file_path)) {
                Storage::disk('public')->delete($portfolio->file_path);
            }
            $filePath = $request->file('file_file')->store('portfolio', 'public');
            $validated['file_path'] = $filePath;
        }

        $portfolio->update($validated);

        return response()->json(['message' => 'Portfolio item updated successfully', 'portfolio' => $portfolio], 200);
    }

    public function createSuccessStory(Request $request)
    {

        /*
        {"title":"AMR specimens referred to regional testing laboratoriesn","description":"jkbhjsvs njhsivbj","impact":"65","beneficiaries":"4000+","quote":"ds","author":"Dr sam","position":"asa","is_active":true,"image":"https://qi-mis.org/logo"}
        */
        
        $validatedData = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'impact' => 'nullable|string|max:255',
            'beneficiaries' => 'nullable|string|max:255',
            'quote' => 'nullable|string',
            'author' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
            'image' => 'nullable',
        ]);

         if ($validatedData->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validatedData->errors(),
            ], 422);
        }
        //image is base64 encoded string, we need to decode it and store it in the storage/app/public/success_stories folder
        //image post sample ,"image":"data:image/jpeg;base64,/9j/4WFVRXhpZgAASUkqAAgAAEABBAAD/4QAiRXhpZgAATU0AKgAAAAgABwESAAMAAAABAAEAAIdpAAQAAAABAAAAJgAAAAAAAqACAAQAAAABAAAAwqADAAQAAAABAAAAwAAAAAD/2wBDAAYEBQYFBAYGBQYHBwYIChAKCgkJChQODwwQFxQYGBcUFhYaHSUfGhsjHBYWICwgIyYnKSopGR8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLf/AABEIAKgBLAMBIgACEAMBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAcAAAECAwQABRESIQYxQVEHEyJhcYEykaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXh5eoKDhIWGh4iJipKTlJWWl5iZmq
        $imagePath = null;
        if ($request->has('image')) {
            $imageData = $request->input('image');
            // Remove data URI prefix if present
            if (strpos($imageData, 'data:') === 0) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
            }
            $decodedImage = base64_decode($imageData);
            $mimeType = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $decodedImage);
            $extension = match($mimeType) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                default => 'jpg',
            };
            $imageName = time() . '.' . $extension;
            $imagePath = 'success_stories/' . $imageName;
            Storage::disk('public')->put($imagePath, $decodedImage);
            $request->merge(['image' => $imagePath]);
            $request->request->set('image', $imagePath);
        }

       //return response()->json(['message' => 'Data received successfully', 'data' => $request->all()], 200);

        $data = $validatedData->validated();

        $successStory = SuccessStory::create(array_merge($data, ['image' => $imagePath]));

        return response()->json(['message' => 'Data received successfully', 'data' => $successStory], 200);
    }

    public function updateSuccessStory($id)
    {
       
        $successStory = SuccessStory::find($id);
        if (!$successStory) {
            return response()->json(['message' => 'Success Story not found'], 404);
        }

        $validatedData = Validator::make(request()->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'impact' => 'sometimes|nullable|string|max:255',
            'beneficiaries' => 'sometimes|nullable|string|max:255',
            'quote' => 'sometimes|nullable|string',
            'author' => 'sometimes|nullable|string|max:255',
            'position' => 'sometimes|nullable|string|max:255',
            'is_active' => 'sometimes|required|boolean',
            'image' => 'sometimes|nullable',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validatedData->errors(),
            ], 422);
        }

        $data = $validatedData->validated();

        // Handle image update if provided
        if (isset($data['image'])) {
            // Delete old image if exists
            if ($successStory->image && Storage::disk('public')->exists($successStory->image)) {
                Storage::disk('public')->delete($successStory->image);
            }

            // Process new image
            $imageData = $data['image'];
            if (strpos($imageData, 'data:') === 0) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
            }
            $decodedImage = base64_decode($imageData);
            $mimeType = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $decodedImage);
            $extension = match($mimeType) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                default => 'jpg',
            };
            $imageName = time() . '.' . $extension;
            $imagePath = 'success_stories/' . $imageName;
            Storage::disk('public')->put($imagePath, $decodedImage);
            $data['image'] = $imagePath;
        }

        $successStory->update($data);

        return response()->json(['message' => 'Success Story updated successfully', 'data' => $successStory], 200);
    }


    public function deleteSuccessStory($id)
    {
        $successStory = SuccessStory::find($id);
        if (!$successStory) {
            return response()->json(['message' => 'Success Story not found'], 404);
        }

        // Optionally delete the image file from storage
        if ($successStory->image && Storage::disk('public')->exists($successStory->image)) {
            Storage::disk('public')->delete($successStory->image);
        }

        $successStory->delete();

        return response()->json(['message' => 'Success Story deleted successfully'], 200);
    }

    public function getSuccessStories()
    {
        $successStories = SuccessStory::where('is_active', true)->get();
        return response()->json($successStories);
    }

    public function getSuccessStoriesAll()
    {
        $successStories = SuccessStory::all();
        return response()->json($successStories);
    }

    public function getSuccessStoryById($id)
    {
        $successStory = SuccessStory::find($id);
        if ($successStory) {
            return response()->json($successStory);
        } else {
            return response()->json(['message' => 'Success Story not found'], 404);
        }
    }
}