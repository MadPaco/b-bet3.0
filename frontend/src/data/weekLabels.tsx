export const weekLabels: { [key: number]: string } = {
    19: 'Wild Card',
    20: 'Divisional',
    21: 'Conference',
    22: 'Super Bowl',
};

export const generateWeekOptions = () => {
    const NFLWEEKS = 22;
    const weeks = [];
    for (let i = 1; i <= NFLWEEKS; i++) {
        const label = weekLabels[i] || `Week ${i}`;
        weeks.push(<option value={i} key={i}>{label}</option>);
    }
    return weeks;
};