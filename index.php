<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <script src="dist/jspsych.js"></script>
  <script src="dist/plugin-fullscreen.js"></script>
  <script src="dist/plugin-html-button-response.js"></script>
  <script src="dist/plugin-html-keyboard-response.js"></script>
  <script src="dist/plugin-image-keyboard-response.js"></script>
  <script src="dist/plugin-survey-text.js"></script>
  <script src="dist/plugin-webgazer-init-camera.js"></script>
  <script src="dist/plugin-webgazer-calibrate.js"></script>
  <script src="dist/plugin-webgazer-validate.js"></script>
  <script src="js/webgazer/webgazer.js"></script>
  <script src="dist/extension-webgazer.js"></script>
  <link rel="stylesheet" href="dist/jspsych.css">
  <link rel="stylesheet" href="./css/style.css">
</head>


<body></body>
<script>

  let ageVal = null;
  let nationalityVal = null;
  let selectedLanguage = null;
  let useEyeTracking = false;

  const fd12Images = get_random_images_from_folder('12', 10);
  const fd14Images = get_random_images_from_folder('14', 10);
  const fd16Images = get_random_images_from_folder('16', 10);

  const image_list = [...fd12Images, ...fd14Images, ...fd16Images];
  shuffleArray(image_list);

  // --------------- Start ---------------

  var fullscreen = {
    type: jsPsychFullscreen,
    fullscreen_mode: true
  };

  var research_info = {
    type: jsPsychHtmlButtonResponse,
    stimulus: `
      <div style="max-width:900px; font-size: 16px; line-height: 1.5;">
        <img src="./images/banner_pareidolia2.png" style="position: absolute; top: 0; left: 0; width:100%;" alt="Pareidolia Game Image">

        <h2 style="margin-top: 200px;">Welcome to the Online Pareidolia Game!</h2>
        
        <h3 style="margin-bottom: 20px;">By participating, you agree to the following:</h3>
        
        <ul style="margin-bottom: 20px;text-align: left;max-width: 627px;">
          <li style="margin-bottom: 10px;">The data collected will be used exclusively for academic and research purposes.</li>
          <li style="margin-bottom: 10px;">Your data will be anonymized and won't be associated with any personal identifiers.</li>
          <li style="margin-bottom: 10px;">The experiment consists of 2 short tasks. The first involves looking at ambiguous images. The second consist in providing semantically distant words. Full description of the tasks will be provided later.</li>
          <li>You can terminate your participation at any time.</li></br>
        </ul>
        
        <p style="margin-top: 20px;"></p>

    `,
    choices: ['Agree'] // index of the "Agree" button
  };

  var demographic_questions = {
    type: jsPsychHtmlButtonResponse,
    stimulus: `
        <div style="max-width:600px;">
            <h3>Please answer the following demographic questions:</h3></br>
            <div style="margin-bottom: 20px;text-align: left;">
              <label for="ageInput" style="width:100px; display: inline-block;">Age</label>
              <input type="number" id="ageInput" name="age" min="1" required><br><br>

              <label for="nationalityInput" style="width:100px; display: inline-block;">Nationality</label>
              <input type="text" id="nationalityInput" name="nationality" placeholder="e.g. American" required><br><br>

              <label for="languageSelect" style="width:100px; display: inline-block;">My native language is</label>
              <select id="languageSelect" name="my native language is">
                  <option value="English">English</option>
                  <option value="Spanish">Spanish</option>
                  <option value="Chinese">Chinese</option>
                  <option value="Hindi">Hindi</option>
                  <option value="Arabic">Arabic</option>
                  <option value="Bengali">Bengali</option>
                  <option value="Portuguese">Portuguese</option>
                  <option value="Russian">Russian</option>
                  <option value="Japanese">Japanese</option>
                  <option value="Punjabi">Punjabi</option>
                  <option value="Javanese">Javanese</option>
                  <option value="Malay">Malay</option>
                  <option value="Telugu">Telugu</option>
                  <option value="Vietnamese">Vietnamese</option>
                  <option value="Korean">Korean</option>
                  <option value="French">French</option>
                  <option value="German">German</option>
                  <option value="Italian">Italian</option>
                  <option value="Dutch">Dutch</option>
                  <option value="Greek">Greek</option>
                  <option value="Other">Other</option>
              </select>
              <br><br>
          </div>
        </div>
    `,
    choices: ['Continue'],
    button_html: '<button class="jspsych-btn" disabled id="continueButton">%choice%</button>',
    on_load: function () {
      function enableIfCompleted() {
        ageVal = document.getElementById('ageInput').value;
        nationalityVal = document.getElementById('nationalityInput').value;
        selectedLanguage = document.getElementById('languageSelect').value;

        if (ageVal && nationalityVal && selectedLanguage) {
          document.getElementById('continueButton').disabled = false;
        }
      }

      document.getElementById('ageInput').addEventListener('input', enableIfCompleted);
      document.getElementById('nationalityInput').addEventListener('input', enableIfCompleted);
      document.getElementById('languageSelect').addEventListener('change', enableIfCompleted);
    },
    on_finish: function (trial) {
      //let responses = JSON.parse(data.responses);
      trial.data = ({
        age: ageVal,
        nationality: nationalityVal,
        primary_language: selectedLanguage
      });
    }
  };

  // --------------- Webgazer (eye tracking) ---------------

  var camera_request = {
    type: jsPsychHtmlButtonResponse,
    trial_id: 'camera_request',
    stimulus: `
    <h2>👀 Ready for some eye-tracking fun in our game? 👀</h2>
    <p>We'll use your webcam to track your eye movements</p>
    <p>helping us understand how you play.</p>
    <h3>Don't worry, we're only interested in your eye movements </h3>
    <p>No images or videos will be recorded.</p></br></br>
  

      `,
    choices: ['Let\'s Go', 'I will Pass'],
    post_trial_gap: 1000,
  };

  var init_camera = {
    type: jsPsychWebgazerInitCamera,
  };

  var calibration_instructions = {
    type: jsPsychHtmlButtonResponse,
    stimulus: `
        <h2>🌟 Great! Now the eye tracker will be calibrated. 🎯</h2>
        <p>Click on each dot as it appears</p>
        <p>keeping your head still and your eyes on the dot.</p>
        </br>
      `,
    choices: ['Click to begin'],
    post_trial_gap: 1000,
  };

  var calibration = {
    type: jsPsychWebgazerCalibrate,
    calibration_points: [[50, 50], [25, 25], [25, 75], [75, 25], [75, 75]],
    repetitions_per_point: 2,
    randomize_calibration_order: true,
    extensions: [
      { type: jsPsychExtensionWebgazer, params: { showGazeDot: true } }
    ],
  };

  var validation_instructions = {
    type: jsPsychHtmlButtonResponse,
    stimulus: `
      <h3>👀 Ready to test the eye tracker? Let's get started! 🚀</h3>
      <p>Just keep your head still and follow the dots with your eyes. No clicks needed! 🎉</p></br>

      `,
    choices: ['Click to begin'],
    post_trial_gap: 1000,
  };

  var validation = {
    type: jsPsychWebgazerValidate,
    validation_points: [[25, 25], [25, 75], [75, 25], [75, 75]],
    show_validation_data: true,
    // data: {
    //   task: 'validate'
    // },
    on_finish: function () {
      jsPsych.extensions['webgazer'].hidePredictions();
    }
  };


  // --------------- Pareidolia Experiment Sequence---------------

  var pareidolia_instructions = {
    type: jsPsychHtmlButtonResponse,
    stimulus: `
    <div>
      <h1>🔍 Pareidolia Game 🔍</h1></br></br>
      <div style="display: flex;">
        <div style="max-width: 327px;margin-right: 30px;width: 47%;"> 
          <img src="./images/example.png" alt="Descriptive Text" style="width: 100%;">
        </div>
        <div style="text-align: left;padding: 28px 0px;">
          <p>Look at each image for <b>10 seconds</b>.</p>
          <p>Try to find hidden shapes or objects in these images!</p>
          <p>When you detect the first percept, <b>press the space bar</b>.</p>
          <p>Then, describe each percept you found using <b>one word per percept</b>.</p>
          <p>Let's see what you discover! 🌟</p>
        </div>
      </div>
      <br/><br/>
    </div>


    `,
    choices: ['Click to begin']
  };

  var experiment = {
    type: jsPsychHtmlKeyboardResponse,
    stimulus: function () {
      var url_img = get_sample_image();
      randomimg = '<img style="max-width: 55%;" src="' + url_img + '">';
      return randomimg;
    },
    choices: [' '],
    trial_duration: 10000,
    response_ends_trial: false,
    on_start: function (trial) {
      trial.data = { onset_time: jsPsych.getTotalTime() };
    },
    on_finish: function (data) {
      data.offset_time = jsPsych.getTotalTime();
    },
    post_trial_gap: 100
  };

  var experiment_eye_tracking = {
    type: jsPsychHtmlKeyboardResponse,
    stimulus: function () {
      var url_img = get_sample_image();
      randomimg = '<img style="max-width: 55%;" src="' + url_img + '">';
      return randomimg;
    },
    choices: [' '],
    trial_duration: 10000,
    response_ends_trial: false,
    on_start: function (trial) {
      trial.data = { onset_time: jsPsych.getTotalTime() };
    },
    on_finish: function (data) {
      data.offset_time = jsPsych.getTotalTime();
    },
    post_trial_gap: 100,
    extensions: [
      {
        type: jsPsychExtensionWebgazer,
        params: { targets: ['#jspsych-image-keyboard-response-stimulus'] }
      }
    ]
  };

  var words = {
    type: jsPsychSurveyText,
    questions: [
      { prompt: 'word 1', columns: 20, required: false, placeholder: 'word 1', name: 'word1' },
      { prompt: 'word 2', columns: 20, required: false, placeholder: 'word 2', name: 'word2' },
      { prompt: 'word 3', columns: 20, required: false, placeholder: 'word 3', name: 'word3' }
    ],
    randomize_question_order: false,
    conditional_function: function () {
      var last_trial_data = jsPsych.data.getLastTrialData().values()[0];
      return last_trial_data.key_press !== null;  // Only display the text boxes if the space bar was pressed in the last trial
    },
    post_trial_gap: 100
  };

  var fixation_cross = {
    type: jsPsychImageKeyboardResponse,
    stimulus: './images/fixation_cross.png', // Set the width as desired; 'height: auto' maintains the aspect ratio
    choices: 'NO_KEYS',
    stimulus_width: 700,
    stimulus_height: 700,
    trial_duration: 1000,
    response_ends_trial: false
  };

  var sequence_eye_tracking = {
    timeline: [fixation_cross, experiment_eye_tracking, words],
    repetitions: 30,
    conditional_function: function () {
      return useEyeTracking;
    }
  }

  var sequence = {
    timeline: [fixation_cross, experiment, words],
    repetitions: 30,
    conditional_function: function () {
      return !useEyeTracking;
    }
  }

  // --------------- End of Experiment---------------

  var DAT_instructions = {
    type: jsPsychHtmlButtonResponse,
    stimulus: `
    <div>
      <h1>Divergent Association Task</h1>
      <p>Please enter 10 words that are as different from each other as possible, in all meanings and uses of the words.</p>
      <div style="text-align: left; margin: auto; max-width: 493px;">
        <h2>📜 Rules</h2>
        <ol style="text-align: left;">
          <li>Only single words in English.</li>
          <li>Only nouns (e.g., things, objects, concepts).</li>
          <li>No proper nouns (e.g., no specific people or places).</li>
          <li>No specialised vocabulary (e.g., no technical terms).</li>
          <li>Think of the words on your own (e.g., do not just look at objects in your surroundings).</li>
        </ol>
    </div>
    </div>

        `,
    choices: ['Click to begin'],
    post_trial_gap: 1000
  };

  var DAT_test = {
    type: jsPsychSurveyText,
    questions: [
      { prompt: 'Word 1', columns: 25, required: true, placeholder: 'word 1' },
      { prompt: 'Word 2', columns: 25, required: true, placeholder: 'word 2' },
      { prompt: 'Word 3', columns: 25, required: true, placeholder: 'word 3' },
      { prompt: 'Word 4', columns: 25, required: true, placeholder: 'word 4' },
      { prompt: 'Word 5', columns: 25, required: true, placeholder: 'word 5' },
      { prompt: 'Word 6', columns: 25, required: true, placeholder: 'word 6' },
      { prompt: 'Word 7', columns: 25, required: true, placeholder: 'word 7' },
      { prompt: 'Word 8', columns: 25, required: true, placeholder: 'word 8' },
      { prompt: 'Word 9', columns: 25, required: true, placeholder: 'word 9' },
      { prompt: 'Word 10', columns: 25, required: true, placeholder: 'word 10' }
    ],
    randomize_question_order: false,
    css_classes: 'hide-labels'
  };

  var done = {
    type: jsPsychHtmlButtonResponse,
    choices: ['CSV', 'JSON'],
    stimulus: `<p>Done!</p><p>If you'd like to download a copy of the data to explore, click the format you'd like below</p>`,
    on_finish: function (data) {
      var all_data = jsPsych.data.get();
      post_sample_data(all_data);
      if (data.response == 0) {
        jsPsych.data.get().localSave('csv', 'webgazer-sample-data.csv');
      }
      if (data.response == 1) {
        jsPsych.data.get().localSave('json', 'webgazer-sample-data.json');
      }
    }
  };

  // --------------- API + functions ---------------

  function post_sample_data(data) {
    const requestOptions = {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
    };
    const url = 'api_post.php';
    fetch(url, requestOptions)
      .then(response => response.json())
      .then(data => {
        console.log('Réponse du serveur PHP :', data);
      })
      .catch(error => {
        console.error('Erreur :', error);
      });
  }

  function get_sample_image() {
    var randomIndex = Math.floor(Math.random() * image_list.length);
    var url = image_list[randomIndex];
    // delete the image from the list
    image_list.splice(randomIndex, 1);
    return url;
  }

  function get_random_images_from_folder(FD, count) {
    // Assuming you have up to 100 images in each folder. Adjust this number if necessary.
    const totalImagesInFolder = 100;
    const selectedIndices = [];
    while (selectedIndices.length < count) {
      const randomIndex = Math.floor(Math.random() * totalImagesInFolder);
      if (!selectedIndices.includes(randomIndex)) {
        selectedIndices.push(randomIndex);
      }
    }
    return selectedIndices.map(index => `images/FD${FD}/FD_${FD}_${index}.png`); // Change the filename format if necessary.
  }

  function shuffleArray(array) {
    for (let i = array.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [array[i], array[j]] = [array[j], array[i]];
    }
  }


  // --------------- init JsPsych ---------------

  var jsPsych = initJsPsych({
    on_finish: function () {
      jsPsych.data.displayData();
    },
    extensions: [
      { type: jsPsychExtensionWebgazer, params: { showGazeDot: false } }
    ]
  });

  var timeline = [];


  var recalibration_button = {
    type: 'html-button-response',
    stimulus: '<p>Would you like to recalibrate?</p>',
    choices: ['Yes', 'No'],
    on_finish: function (data) {
      if (data.button_pressed === 0) {
        jsPsych.resumeExperiment();
      } else {
        jsPsych.endExperiment();
      }
    },
  };

  // Ajoutez cette étape à votre timeline avant la fin de l'expérience
  // var recalibration_timeline = {
  //   timeline: [recalibration_button],
  //   conditional_function: function () {
  //     // Vous pouvez définir une condition pour afficher cette étape
  //     // par exemple, basée sur le résultat de la validation
  //     var validation_data = jsPsych.data.get().filter({ task: 'validate' }).values()[0];
  //     return validation_data.percent_in_roi.some(function (x) {
  //       var minimum_percent_acceptable = 50;
  //       return x < minimum_percent_acceptable;
  //     });
  //   },
  // };

  var if_camera = {
    timeline: [calibration_instructions, init_camera, calibration, validation_instructions, validation],
    conditional_function: function () {
      // get the data from the previous trial,
      // and check which key was pressed
      var data = jsPsych.data.get().last(1).values()[0];
      if (data.response == 1) {
        return false;
      } else {
        return true;
      }
    }
  }



  var after_if_trial = {
    type: jsPsychHtmlKeyboardResponse,
    stimulus: 'Pareidolie'
  }
  timeline.push(research_info);
  timeline.push(demographic_questions);
  timeline.push(camera_request);
  timeline.push(if_camera);
  timeline.push(pareidolia_instructions);
  // --------- conditional eye tracking ---------
  timeline.push(sequence_eye_tracking);
  timeline.push(sequence);
  // --------------------------------------------
  timeline.push(DAT_instructions);
  timeline.push(DAT_test);
  timeline.push(done);

  jsPsych.run(timeline);

</script>

</html>