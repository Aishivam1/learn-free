@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Global Leaderboard -->
        <div class="mb-5">
            <h2 class="text-center mb-4">Global Leaderboard</h2>
            <div class="leaderboard-container">
                @if (count($globalLeaderboard) >= 3)
                    <div class="top-3-container">
                        <!-- 2nd Place - Left Side -->
                        <div class="leader second-place">
                            <div class="rank-badge">2</div>
                            <img src="{{ asset('avatar/' . ($globalLeaderboard[1]->avatar ?? 'default.png')) }}"
                                alt="Avatar">
                            <p>{{ $globalLeaderboard[1]->name }}</p>
                            <p class="points">{{ $globalLeaderboard[1]->points }} pts</p>
                        </div>

                        <!-- 1st Place - Center -->
                        <div class="leader first-place">
                            <div class="rank-badge">1</div>
                            <img src="{{ asset('avatar/' . ($globalLeaderboard[0]->avatar ?? 'default.png')) }}"
                                alt="Avatar">
                            <p>{{ $globalLeaderboard[0]->name }}</p>
                            <p class="points">{{ $globalLeaderboard[0]->points }} pts</p>
                        </div>

                        <!-- 3rd Place - Right Side -->
                        <div class="leader third-place">
                            <div class="rank-badge">3</div>
                            <img src="{{ asset('avatar/' . ($globalLeaderboard[2]->avatar ?? 'default.png')) }}"
                                alt="Avatar">
                            <p>{{ $globalLeaderboard[2]->name }}</p>
                            <p class="points">{{ $globalLeaderboard[2]->points }} pts</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Remaining Users -->
            <table class="table mt-4">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Avatar</th>
                        <th>User</th>
                        <th>Points</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($globalLeaderboard as $index => $user)
                        @if ($index >= 3)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <img src="{{ asset('avatar/' . ($user->avatar ?? 'default.png')) }}"
                                        class="leaderboard-avatar" alt="{{ $user->name }}'s avatar">
                                </td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->points }} pts</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Weekly Leaderboard -->
        <div class="mb-5">
            <h2 class="text-center mb-4">Weekly Leaderboard</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Avatar</th>
                        <th>User</th>
                        <th>Points</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($weeklyLeaderboard as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <img src="{{ asset('avatar/' . ($user->avatar ?? 'default.png')) }}"
                                    class="leaderboard-avatar" alt="{{ $user->name }}'s avatar">
                            </td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->points }} pts</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Category Leaderboard -->
        <div class="mb-5">
            <h2 class="text-center mb-4">Category Leaderboard</h2>
            <form method="GET" action="{{ route('leaderboard') }}" id="categoryForm" class="mb-4">
                <div class="d-flex justify-content-center">
                    <select name="category" id="categorySelect" class="form-select w-auto">
                        <option value="courses" {{ $category == 'courses' ? 'selected' : '' }}>Courses</option>
                        <option value="quizzes" {{ $category == 'quizzes' ? 'selected' : '' }}>Quizzes</option>
                        <option value="discussions" {{ $category == 'discussions' ? 'selected' : '' }}>Discussions</option>
                    </select>
                </div>
            </form>

            <table class="table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Avatar</th>
                        <th>User</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categoryLeaderboard as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <img src="{{ asset('avatar/' . ($user->avatar ?? 'default.png')) }}"
                                    class="leaderboard-avatar" alt="{{ $user->name }}'s avatar">
                            </td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->total }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* General Leaderboard Styling */
        .leaderboard-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #007bff;
        }

        .leaderboard-container {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 30px;
            margin-bottom: 30px;
        }

        .top-3-container {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 30px;
            margin-bottom: 40px;
            position: relative;
        }

        .leader {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .leader img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid #007bff;
            object-fit: cover;
            transition: transform 0.3s ease-in-out;
        }

        .leader:hover img {
            transform: scale(1.1);
        }

        .table {
            width: 100%;
            background: #ffffff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
        }

        .table th {
            background: #007bff;
            color: white;
            padding: 14px;
        }

        .table td {
            padding: 12px 16px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        .table tbody tr:hover {
            background: rgba(0, 123, 255, 0.1);
        }

        .small-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #007bff;
            margin-right: 8px;
        }

        .nav-tabs .nav-link {
            color: #007bff;
            font-weight: bold;
        }

        .nav-tabs .nav-link.active,
        .nav-tabs .nav-link:hover {
            background: #007bff;
            color: white;
            border-radius: 5px;
        }

        .leader {
            position: relative;
        }



        .leader p {
            margin: 8px 0;
            color: #007bff;
            font-weight: bold;
        }

        .leader .points {
            font-size: 16px;
            color: #6c757d;
        }

        .first-place {
            order: 2;
            transform: scale(1.3);
            z-index: 5;
        }

        .second-place {
            order: 1;
            margin-top: 25px;
        }

        .third-place {
            order: 3;
            margin-top: 45px;
        }



        /* Table Styling */
        .table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            overflow: hidden;
        }

        .table th {
            background: #007bff;
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 14px;
        }

        .table td {
            padding: 12px 16px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        .table tbody tr:hover {
            background: rgba(0, 123, 255, 0.1);
        }

        /* Small Avatars for Table */
        .small-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #007bff;
            margin-right: 8px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .top-3-container {
                flex-direction: column;
                align-items: center;
            }

            .leader img {
                width: 100px;
                height: 100px;
            }

            .first-place,
            .second-place,
            .third-place {
                margin-bottom: 10px;
                transform: scale(1);
            }
        }

        .rank-badge {
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 30px;
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            z-index: 10;
        }


        @media (max-width: 768px) {
            .leaderboard-container {
                flex-direction: column;
                align-items: center;
            }

            .leader img {
                width: 80px;
                height: 80px;
            }

            .second-place,
            .third-place {
                margin-top: 10px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.getElementById("categorySelect").addEventListener("change", function() {
            document.getElementById("categoryForm").submit();
        });
    </script>
@endpush
