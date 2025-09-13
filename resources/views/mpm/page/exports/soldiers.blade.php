<table>
    <thead>
        <tr>
            <th>Army No</th>
            <th>Full Name</th>
            <th>Rank</th>
            <th>Company</th>
            <th>Status</th>
            <th>Joining Date</th>
            <th>Profile Completion</th>
            <th>Phone</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($soldiers as $soldier)
            <tr>
                <td>{{ $soldier->army_no }}</td>
                <td>{{ $soldier->full_name }}</td>
                <td>{{ $soldier->rank->name ?? 'N/A' }}</td>
                <td>{{ $soldier->company->name ?? 'N/A' }}</td>
                <td>{{ app('App\Services\Export\ExportService')->getStatusText($soldier) }}</td>
                <td>
                    {{ $soldier->joining_date ? \Carbon\Carbon::parse($soldier->joining_date)->format('Y-m-d') : 'N/A' }}
                </td>
                <td>{{ app('App\Services\Export\ExportService')->calculateProfileCompletion($soldier) }}%</td>
                <td>{{ $soldier->phone ?? 'N/A' }}</td>
                <td>{{ $soldier->email ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
