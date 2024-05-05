@extends("template.main")
@section('title', 'Contact Us')
@section('body')

<form id="deleteForm" action="{{ route('delete.contact') }}" method="post">
    @csrf
    @if(isset($contacts) && is_array($contacts) && count($contacts) > 0)
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Message</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contacts as $contact)
            <tr>
                <td>{{ $contact->id }}</td>
                <td>{{ $contact->name }}</td>
                <td>{{ $contact->email}}</td>
                <td>{{ $contact->phone}}</td>
                <td>{{ $contact->message}}</td>
                <td>
                    <button type="submit" class="btn btn-danger" onclick="deleteSelected()" name="contactIdToDelete" value="{{ $contact->id }}">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No contact data available.</p>
    @endif
</form>


<script>
    function deleteSelected() {
        if (confirm('Are you sure you want to delete the selected items?')) {
            document.getElementById('deleteForm').submit();
        }
    }
</script>

@endsection
