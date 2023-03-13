@extends('layouts.app')

@section('title', 'Technologies')

@section('content')
<header class="d-flex align-items-center justify-content-between">
  <h1>Technologies List</h1>
  {{-- LINK TO CREATE --}}
  <a href="{{route('admin.technologies.create')}}" class="btn btn-small btn-warning">Create new Technology</a>
</header>

{{-- TABLE --}}
<table class="table text-center">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">Label</th>
        <th scope="col">Color</th>
        <th scope="col">Control Panel</th>
      </tr>
    </thead>

    <tbody>
        @forelse($technologies as $technology)
        <tr>
            <th scope="row">{{$technology->id }}</th>
            <td>{{$technology->label }}</td>
            {{-- AL COLOR BACKGROUND IF arriva il colore metti  -------------CANCELLA POI--------------------}}
            <td class="bg-{{$technology->color}}" style="background-color : {{$technology->color}}"></td>
            <td class="d-flex justify-content-end">
                {{-- BOTTON TO technology EDIT --}}
                <a class="btn btn-warning mx-2" href="{{route('admin.technologies.edit', $technology->id)}}">
                  <i class=" fa-solid fa-pencil"></i>
                </a>

                {{-- BUTTON DELETE --}}
                <form action="{{route('admin.technologies.destroy', $technology->id)}}" method="POST" class="delete-form" data-entity='technology'>
                  @method('DELETE')
                  {{-- TOKEN --}}
                  @csrf
                  <button type="submit" class="btn btn-small btn-danger" >
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </form>
            </td>
          </tr>
        @empty
        <tr>
            <td scope='row' colspan="5">No Types found</td>
        </tr>
        @endforelse
    </tbody>
  </table>

  {{-- PAGINATION se type ha la pagination  --}}
  <div class="d-flex">
    @if($technologies->hasPages())
      {{$technologies->links() }}
    @endif
  </div>
@endsection

