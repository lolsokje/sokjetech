<template>
    <div class="row mb-3 mt-4">
        <h4>Rating multipliers</h4>

        <small>
            The multipliers are used during RNG rolls to affect how much each rating matters in the total RNG roll.
            For example, a team multiplier of 1.5 means the team rating is multiplied by 1.5 for each RNG
            roll.
        </small>

        <div class="col-3">
            <label for="team_multiplier" class="form-label">Team multiplier</label>
            <div class="input-group">
                <input id="team_multiplier"
                       type="number"
                       :value="team"
                       class="form-control"
                       step="0.01"
                       @input="$emit('update:team', parseFloat($event.target.value))"
                >
                <span class="input-group-text">%</span>
            </div>
        </div>

        <div class="col-3">
            <label for="engine_multiplier" class="form-label">Engine multiplier</label>
            <div class="input-group">
                <input id="engine_multiplier"
                       type="number"
                       :value="engine"
                       class="form-control"
                       step="0.01"
                       @input="$emit('update:engine', parseFloat($event.target.value))"
                >
                <span class="input-group-text">%</span>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
interface Props {
    team: number,
    engine: number,
}

const props = defineProps<Props>();

defineEmits([ 'update:team', 'update:engine' ]);
</script>
