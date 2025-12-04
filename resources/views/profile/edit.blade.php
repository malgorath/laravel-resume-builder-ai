@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Edit Profile</h2>
    </div>
    <div class="card-body">
        @if(session('new_skills'))
            <div id="skillPopup" class="modal fade show" style="display: block;" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm New Skills</h5>
                            <button type="button" class="btn-close" onclick="closeSkillPopup()"></button>
                        </div>
                        <div class="modal-body">
                            <p>The following skills were detected in your resume:</p>
                            <ul class="list-group">
                                @foreach(session('new_skills') as $skill)
                                    <li class="list-group-item">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="confirmed_skills[]" value="{{ $skill }}" id="skill_{{ $loop->index }}" checked>
                                            <label class="form-check-label" for="skill_{{ $loop->index }}">{{ $skill }}</label>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="submitNewSkills()">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>

            <script>
                function closeSkillPopup() {
                    document.getElementById('skillPopup').style.display = 'none';
                    document.querySelector('.modal-backdrop').remove();
                }

                function submitNewSkills() {
                    let selectedSkills = [];
                    document.querySelectorAll('input[name="confirmed_skills[]"]:checked').forEach(skill => {
                        selectedSkills.push(skill.value);
                    });

                    fetch("{{ route('profile.skills.confirm') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ skills: selectedSkills })
                    }).then(response => {
                        location.reload();
                    });
                }
            </script>
        @endif

        <form method="POST" action="{{ route('profile.details.update', $user->id) }}">
            @csrf
            @method('POST')

            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $user->userDetail->address ?? '') }}">
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->userDetail->phone ?? '') }}">
            </div>

            <div class="mb-3">
                <label for="linkedin" class="form-label">LinkedIn</label>
                <input type="text" class="form-control" id="linkedin" name="linkedin" value="{{ old('linkedin', $user->userDetail->linkedin ?? '') }}">
            </div>

            <button type="submit" class="btn btn-primary">Save Details</button>
        </form>

        <hr class="my-4">

        <h3 class="mb-3">Change Password</h3>
        <form method="POST" action="{{ route('password.update') }}" class="mb-4">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="current_password" class="form-label">Current Password</label>
                <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" id="current_password" name="current_password" required>
                @error('current_password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" id="password" name="password" required>
                @error('password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
            </div>

            @if (session('status') === 'password-updated')
                <div class="alert alert-success">Password updated successfully!</div>
            @endif

            <button type="submit" class="btn btn-primary">Update Password</button>
        </form>

        <hr class="my-4">

        <h3 class="mb-3">Skills</h3>

        <form method="POST" action="{{ route('profile.skills.add', $user->id) }}" class="mb-3">
            @csrf
            <div class="input-group">
                <input type="text" name="skill" class="form-control" placeholder="New skill" required>
                <button type="submit" class="btn btn-success">Add</button>
            </div>
        </form>

        <ul class="list-group">
            @foreach($user->userSkills as $userSkill)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $userSkill->skill->name ?? ($userSkill->skill ?? 'Unknown Skill') }}
                    <form method="POST" action="{{ route('profile.skills.delete', $userSkill->id) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
