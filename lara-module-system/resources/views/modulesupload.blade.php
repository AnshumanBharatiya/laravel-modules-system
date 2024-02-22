@extends('layout')

@section('title', 'Upload Modules Zip')

@section('content')
    <form action="{{route('upload.zip')}}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="container mt-3">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card card-upload">
                        <div class="card-body">
                            <h5 class="card-title">Upload a Zip File</h5>
                            <p class="card-text">Select a zip file from your computer to upload.</p>
                            <div class="file-upload-wrapper">
                                <input type="file" class="file-input form-control" name="zipFile" id="zipFile" accept=".zip">
                                
                                @error('zipFile')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                <div class=" text-center mt-2">
                                    <input type="submit" name="submit" value="UPLOAD" class="btn btn-outline-primary">
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection