import DevelopmentItem from '@/Interfaces/DevelopmentItem';

export default interface DevelopmentDriver extends DevelopmentItem {
    full_name: string,
    number: number,
    team_name: string,
    team_style: string,
    accent_colour: string,
}
