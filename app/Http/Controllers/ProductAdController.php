<?php
namespace App\Http\Controllers;
use App\Models\Notification;use App\Models\Product;use App\Models\ProductAd;use App\Models\User;use Illuminate\Http\Request;use Illuminate\Http\RedirectResponse;use Illuminate\Support\Facades\Storage;use Illuminate\View\View;
class ProductAdController extends Controller {
public function farmerIndex():View{$ads=ProductAd::where('farmer_id',auth()->id())->with('product')->latest()->paginate(12);$products=Product::where('farmer_id',auth()->id())->where('is_moderated',true)->where('status','available')->orderBy('name')->get();return view('farmer.ads.index',compact('ads','products'));}
public function store(Request $r):RedirectResponse
{
    $r->validate([
        'product_id'=>['required','integer','exists:products,id'],
        'title'=>['required','string','max:160'],
        'description'=>['required','string','max:1200'],
        'image'=>['nullable','image','max:4096'],
        'image_url'=>['nullable','url:http,https','max:2048'],
        'use_product_image'=>['nullable','boolean'],
        'discount_price'=>['required','numeric','min:0.01'],
    ]);
    $p=Product::where('farmer_id',auth()->id())->where('is_moderated',true)->where('status','available')->findOrFail($r->integer('product_id'));
    if((float)$r->discount_price >= (float)$p->price)return back()->withErrors(['discount_price'=>'Offer price must be lower than the current product price.'])->withInput();
    $useProductImage=$r->boolean('use_product_image');
    if($useProductImage && !$p->image)return back()->withErrors(['use_product_image'=>'The selected product does not have a photo. Upload an image or provide an image URL instead.'])->withInput();
    if(!$useProductImage && !$r->hasFile('image') && !$r->filled('image_url'))return back()->withErrors(['image'=>'Choose â€œUse this productâ€™s imageâ€, upload an image, or paste an image URL.'])->withInput();
    if(ProductAd::where('product_id',$p->id)->whereIn('status',['pending','approved','removal_requested'])->exists())return back()->withErrors(['product_id'=>'This product already has an active or pending ad.'])->withInput();
    $ad=ProductAd::create([
        'farmer_id'=>auth()->id(),'product_id'=>$p->id,'title'=>$r->title,'description'=>$r->description,
        'image_path'=>$useProductImage ? null : $r->file('image')?->store('product-ads','public'),
        'image_url'=>$useProductImage || $r->hasFile('image') ? null : $r->input('image_url'),
        'original_price'=>$p->price,'discount_price'=>$r->discount_price,'status'=>'pending',
    ]);
    foreach(User::where('role','admin')->get() as $u)Notification::send($u->id,'Product ad needs review',$ad->title.' was submitted by '.auth()->user()->name.'.','product_ad','/admin/ads');
    return redirect()->route('farmer.ads.index')->with('success','Your ad was sent to the admin for approval.');
}public function requestRemoval(ProductAd $ad):RedirectResponse{abort_unless($ad->farmer_id===auth()->id(),403);if(!in_array($ad->status,['pending','approved'],true))return back()->with('warning','A removal request cannot be sent for this ad in its current status.');$ad->update(['status'=>'removal_requested','removal_requested_at'=>now()]);foreach(User::where('role','admin')->get() as $u)Notification::send($u->id,'Ad removal requested',auth()->user()->name.' asked to remove â€œ'.$ad->title.'â€.','product_ad','/admin/ads');return back()->with('success','Your removal request was sent to the admin.');}
public function adminIndex():View{$ads=ProductAd::whereIn('status',['pending','approved','removal_requested'])->with(['product','farmer'])->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'removal_requested' THEN 1 WHEN 'approved' THEN 2 ELSE 3 END")->orderBy('sort_order')->latest()->paginate(20);return view('admin.ads.index',compact('ads'));}
public function update(Request $r,ProductAd $ad):RedirectResponse{$d=$r->validate(['action'=>['required','in:approve,reject,remove'],'sort_order'=>['nullable','integer','min:1','max:9999'],'admin_note'=>['nullable','string','max:1000']]);$ad->sort_order=$d['sort_order']??$ad->sort_order;$ad->admin_note=$d['admin_note']??$ad->admin_note;if($d['action']==='approve'){$ad->status='approved';$ad->removal_requested_at=null;$msg='Your product ad â€œ'.$ad->title.'â€ was approved and is now visible on CropCircle.';$type='success';}elseif($d['action']==='reject'){$ad->status='rejected';$msg='Your product ad â€œ'.$ad->title.'â€ was not approved.'.($ad->admin_note?' Admin note: '.$ad->admin_note:'');$type='warning';}else{$ad->status='removed';$msg='Your product ad â€œ'.$ad->title.'â€ was removed by the admin.';$type='info';}$ad->save();Notification::send($ad->farmer_id,'Product ad updated',$msg,'product_ad','/farmer/ads');if($d['action']==='remove'&&$ad->image_path)Storage::disk('public')->delete($ad->image_path);return back()->with('success','Ad status and display priority updated.');}}