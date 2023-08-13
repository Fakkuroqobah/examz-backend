<table>
  <thead>
    <tr>
      <th>NIS</th>
      <th>Kelas</th>
      <th>Nama</th>
      <th>Nilai Pilihan Ganda</th>
      <th>Nilai Esai</th>
      <th>Total Nilai</th>
    </tr>
  </thead>
  <tbody>
  @foreach($data as $row)
    <tr>
      <td>{{ $row['nis'] }}</td>
      <td>{{ $row['class'] }}</td>
      <td>{{ $row['name'] }}</td>
      <td>{{ $row['score_choice'] }}</td>
      <td>{{ $row['score_essai'] }}</td>
      <td>{{ $row['score_choice'] + $row['score_essai'] }}</td>
    </tr>
  @endforeach
  </tbody>
</table>