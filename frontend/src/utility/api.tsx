export async function fetchTeamInfo(team: string) {
  const token = localStorage.getItem('token');
  const response = await fetch(
    `http://127.0.0.1:8000/backend/team/?favTeam=${team}`,
    {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    },
  );
  if (!response.ok) {
    throw new Error(`HTTP error! status: ${response.status}`);
  }
  return await response.json();
}
