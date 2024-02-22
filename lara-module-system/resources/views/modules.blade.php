@extends('layout')

@section('title', 'Modules Page')

@section('content')    
    <div class="card mb-3" >
        <div class="card-body text-center">
        
            <a href="{{ route('modules.upload') }}" class="btn btn-primary ">Add New Module</a>
        </div>
    </div>

    @foreach($modules as $module)

        <div class="card mb-3" style="width: 18rem;">
            <div class="card-body ">
                <h5 class="card-title">{{ $module->getName() }}</h5>
                <p class="card-text">{{ $module->getDescription() }}</p>
                @if($module->isEnabled())
                    Status :<span class="badge badge-success my-2" style="background:#3c763d">Enable</span>   
                @else
                    Status :<span class="badge badge-danger my-2" style="background:#c7254e">Disable</span>  
                @endif
                <div class="btn-group" role="group" aria-label="Module Actions">
                    @if($module->isEnabled())
                        <a href="{{ route('modules.disable', ['moduleName' => $module->getName()]) }}" class="btn btn-danger">Disable</a>
                    @else
                        <a href="{{ route('modules.enable', ['moduleName' => $module->getName()]) }}" class="btn btn-success">Enable</a>
                    @endif
                    <a href="{{ route('modules.export', ['moduleName' => $module->getName()]) }}" class="btn btn-primary mx-2">Export</a>
                    <a href="{{ route('modules.delete', ['moduleName' => $module->getName()]) }}" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    @endforeach
   
@endsection