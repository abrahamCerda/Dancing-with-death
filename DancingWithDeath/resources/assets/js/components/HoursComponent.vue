<template>
    <transition name="slide-fade" v-on:beforeEnter="otherDatePicked">
    <div v-show="showFreeHours" id="free-hours-container">
        <transition name="slide-fade" v-on:afterLeave="showSecondStep">
        <h3 v-show="firstStep" id="instruction">
            {{instructionMessage}}
        </h3>
        </transition>
        <div class="free-hour-container" v-for="(hour,index) in freeHours" v-model="freeHours">
            <div class="free-hour-item" v-on:click="hourSelected(hour)" v-bind:class="{selected : hour.active}">
                {{hour.startHour.startTime + ":00 " + hour.startHour.period}} to {{hour.endHour.endTime + ":00 " + hour.endHour.period}}
            </div>
        </div>
        <transition name="slide-fade">
            <div v-show="showInputEmail" id="input-button-container">
                <div id="input-container">
                    <input type="email" id="email-input" v-model="inputMailValue" required="required" placeholder="my_email@example.com"/>
                </div>
                <div id="button-container">
                    <button type="submit" class="custom-button" v-on:click="scheduleAppointment">
                        Schedule
                    </button>
                </div>
            </div>
        </transition>
    </div>
    </transition>
</template>

<script>
    //we import momentjs
    var moment = require('moment');
    //we import sweet alert
    import swal from 'sweetalert';
    export default {
        name: 'hours',
        //The selectedDate is passed from appointment parent component
        props: ['showFreeHours', 'freeHours', 'selectedDate'],
        data () {
            return {
                selectedHour: '',
                firstStep: true,
                instructionMessage: '',
                showInputEmail: false,
                inputMailValue: '',
                selectedHourIndex :0,
            }
        },
        //we watch the change of the selected date value from the parent
        watch: {
          selectedDate: function(val){
              //we use setTimeout to match the change of the value with the transition duration = 0.3s or 500ms
              setTimeout(function(){
                  if(this.freeHours.length !== 0){
                      this.instructionMessage= "Please select one of the followings free hours on " + val +" to make your appointment with the Death:"
                  }
                  else{
                      this.instructionMessage= "There's no free hours on " + val +" to make your appointment with the Death. Please select another date."
                  }
                  this.showInputEmail= false;
              }, 300);
          }
        },
        methods: {
            hourSelected: function(selectedHour) {
                //we desactive anothers hours actived
                let i;
                for (i=0; i < this.freeHours.length; i++){
                    this.freeHours[i].active= false;
                }
                //we active the selected hour
                selectedHour.active=true;
                //we close the instruction message
                this.firstStep = false;
                this.showInputEmail = true;
                this.selectedHour = selectedHour;
                this.selectedHourIndex = selectedHour.id;


            },
            //When the transition of the instruction message has ended
            showSecondStep: function (el) {
                //we change the message
                this.instructionMessage = "Enter your email below to schedule your appointment on " +this.selectedDate +" from "
                    +this.selectedHour.startHour.startTime + ":00 " + this.selectedHour.startHour.period + " to "+this.selectedHour.endHour.endTime + ":00 " + this.selectedHour.endHour.period+":";
                //we show the new message
                this.firstStep = true;
            },
            //when the whole component template is entering in the transition
            //this happens when the user picks another date from the calendar
            otherDatePicked: function (el){
                //We cgane the instructions message
                if(this.freeHours.length !== 0){
                    this.instructionMessage= "Please select one of the followings free hours on " +this.selectedDate +" to make your appointment with the Death:"
                }
                else{
                    this.instructionMessage= "There's no free hours on " + this.selectedDate +" to make your appointment with the Death. Please select another date."
                }
                //and we hide the input and button
                this.showInputEmail=false;
            },
            //In this method we sent the post request to the api
            scheduleAppointment(){
                let vm = this;
                //we have the selected date and hour
                //We pass the hour to 24 hours format
                console.log("realTime:")
                console.log(this.to24FormatString(this.selectedHour))
                axios.post('appointments', {
                    date: moment(this.selectedDate).format('YYYY-MM-DD'),
                    time: this.to24FormatString(this.selectedHour),
                    contact_email: this.inputMailValue,
                })
                    .then(function (response) {
                        //If we got the response
                        console.log(response);
                        swal("Congrats!", response.data.message.toString(), "success");
                        //we inform the parent component that the appointment is scheduled
                        vm.$emit('appointmentScheduled');
                        //and we close the input and button
                        //and clean the input value
                        vm.showInputEmail = false
                        vm.inputMailValue = ''
                    })
                    .catch(function (errors) {
                        if(errors.response.status === 422){
                            console.log("Objeto response data: ")
                            console.log(errors.response.data.errors)
                            let error;
                            for(error in errors.response.data.errors){
                                //we set the custom error
                                let errorMessage = errors.response.data.errors[error];
                                swal("Oops!", errorMessage.toString(), "error");
                            }
                        }else{
                            console.log(errors)
                        }
                    })
            },
            //this method convert a hour into 24 hours format, with the form: hh:mm:ss
            to24FormatString(hour) {
                let hourAux;
                //Special cases for am and pm hour are considered
                if(hour.startHour.period === 'pm'){
                    if(hour.startHour.startTime !== 12){
                        hourAux = hour.startHour.startTime + 12+":00:00";
                    }
                    else if(hour.startHour.startTime === 12){
                        hourAux= 12 + ":00:00";
                    }
                }
                else{
                    if(hour.startHour.startTime < 10){
                        hourAux = "0"+hour.startHour.startTime +":00:00";
                    }
                    else{
                        hourAux = hour.startHour.startTime+":00:00";
                    }
                }
                return hourAux;
            }
        },
        //before the view is rendered
        created(){

        }
    }
</script>

<style type="text/css">
    *{
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
    }


    #free-hours-container{
        width: 100%;
        height:auto;
        overflow-x:hidden;
    }

    .free-hour-container{
        width: calc(100%/3);
        display:inline-block;
        float: left;
        padding: 2.5px;
    }

    .free-hour-item{
        padding: 10px;
        color:white;
        border: 1px solid white;
        border-radius: 5px;
        transition: background-color .2s, transform .2s;
    }

    .free-hour-item:hover{
        background-color: rgba(255,255,255, 0.5);
        transform: translate(0, -5px);
        cursor:pointer;
    }

    .selected {
        background-color: rgba(255,255,255, 0.5);
    }


    #instruction{
        width: 100%;
        color: white;
        text-align:left;
        padding: 0 2.5px;
        margin-top: 0;
        font-weight: 700;
    }

    #input-button-container{
        width:100%;
        padding-top: 25px;
        overflow-x: hidden;
    }

    #input-container{
        width: 70%;
        padding-right: 10px;
        float:left;
        display:inline-block;
    }

    #email-input{
        width:100%;
        padding: 10px;
        border-radius:5px;
        outline:none;
        transition: background-color .2s;
        border: 0;
        color: black;
        background-color: rgba(255,255,255,0.5);
    }

    #email-input:focus{
        background-color: white;
    }

    #button-container{
        width:30%;
        float:left;
        display:inline-block;
    }

    .custom-button{
        width:100%;
        padding: 10px;
        border-radius: 5px;
        border:0;
        outline:none;
        transition: background-color .2s, color .2s, text-transform .2s;
        background-color: rgba(0,0,0,0.5);
        color: red;
        font-weight: bold;
    }
    .custom-button:hover{
        cursor: pointer;
        color: black;
        background-color: red;
        text-transform: uppercase;
    }

    .slide-fade-enter-active {
        transition: all .3s ease;
    }
    .slide-fade-leave-active {
        transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0);
    }
    .slide-fade-enter, .slide-fade-leave-to
        /* .slide-fade-leave-active below version 2.1.8 */ {
        transform: translateX(10px);
        opacity: 0;
    }

    /*Media query for responsive design*/
    @media screen and (max-width: 781px) {
        #input-container, #button-container{
            width:100%;
            float:none;
            display:block;
            padding-right:10px;
        }
        #input-container{
            padding-bottom:5px;
        }
    }
</style>