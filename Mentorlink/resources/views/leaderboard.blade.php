@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="tab-content mt-4">
            <!-- Global Leaderboard -->
            <div id="global" class="tab-pane fade show active">
                <h2 class="text-center">Global Leaderboard</h2>
                <div class="leaderboard-container">
                    @if (count($globalLeaderboard) >= 3)
                        <div class="top-3-container">
                            <!-- 2nd Place -->
                            <div class="leader second-place">
                                <img src="{{ asset('avatar/' . ($globalLeaderboard[1]->avatar ?? 'default.png')) }}" alt="Avatar">
                                <p>{{ $globalLeaderboard[1]->name }} ({{ $globalLeaderboard[1]->points }} pts)</p>
                            </div>

                            <!-- 1st Place -->
                            <div class="leader first-place">
                                <img src="{{ asset('avatar/' . ($globalLeaderboard[0]->avatar ?? 'default.png')) }}" alt="Avatar">
                                <p>{{ $globalLeaderboard[0]->name }} ({{ $globalLeaderboard[0]->points }} pts)</p>
                            </div>

                            <!-- 3rd Place -->
                            <div class="leader third-place">
                                <img src="{{ asset('avatar/' . ($globalLeaderboard[2]->avatar ?? 'default.png')) }}" alt="Avatar">
                                <p>{{ $globalLeaderboard[2]->name }} ({{ $globalLeaderboard[2]->points }} pts)</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Remaining Users -->
                <table class="table mt-4">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>User</th>
                            <th>Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($globalLeaderboard as $index => $user)
                            @if ($index >= 3) {{-- Display users ranked 4th and below --}}
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <img src="{{ asset('avatar/' . ($user->avatar ?? 'default.png')) }}" class="small-avatar">
                                        {{ $user->name }}
                                    </td>
                                    <td>{{ $user->points }} pts</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>


            <!-- Weekly Leaderboard -->
            <div id="weekly" class="tab-pane fade">
                <h2 class="text-center">Weekly Leaderboard</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Rank</th>
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
                                        class="small-avatar">
                                    {{ $user->name }}
                                </td>
                                <td>{{ $user->weekly_points }} pts</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Category Leaderboard -->
            <div id="category" class="tab-pane fade">
                <h2 class="text-center">Category Leaderboard</h2>
                <form method="GET" action="{{ route('leaderboard') }}" id="categoryForm">
                    <label>Select Category:</label>
                    <select name="category" id="categorySelect">
                        <option value="courses" {{ $category == 'courses' ? 'selected' : '' }}>Courses</option>
                        <option value="quizzes" {{ $category == 'quizzes' ? 'selected' : '' }}>Quizzes</option>
                        <option value="discussions" {{ $category == 'discussions' ? 'selected' : '' }}>Discussions</option>
                    </select>
                </form>

                <table class="table mt-3">
                    <thead>
                        <tr>
                            <th>Rank</th>
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
                                        class="small-avatar">
                                    {{ $user->name }}
                                </td>
                                <td>{{ $user->total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        /* General Leaderboard Styling */
        .leaderboard-container {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 30px;
            margin-bottom: 30px;
        }

        .leader {
            text-align: center;
            position: relative;
        }

        .leader img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid #007bff;
            padding: 5px;
            background: white;
            transition: transform 0.3s ease-in-out;
        }

        .leader:hover img {
            transform: scale(1.1);
        }

        .first-place {
            transform: scale(1.3);
        }

        .second-place,
        .third-place {
            margin-top: 20px;
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
        }  .top-3-container {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 30px;
            margin-bottom: 30px;
            text-align: center;
        }

        .leader {
            position: relative;
        }

        .leader img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid #007bff;
            padding: 5px;
            background: white;
            transition: transform 0.3s ease-in-out;
        }

        .leader:hover img {
            transform: scale(1.1);
        }

        .leader p {
            font-weight: bold;
            color: #007bff;
            margin-top: 8px;
            font-size: 18px;
        }

        /* Positioning */
        .first-place {
            transform: scale(1.3);
            order: 1;
        }

        .second-place {
            order: 2;
            margin-top: 30px;
        }

        .third-place {
            order: 3;
            margin-top: 30px;
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
                width: 80px;
                height: 80px;
            }

            .second-place,
            .third-place {
                margin-top: 10px;
            }
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

    <script>
        document.getElementById("categorySelect").addEventListener("change", function() {
            document.getElementById("categoryForm").submit();
        });
    </script>
@endsection
