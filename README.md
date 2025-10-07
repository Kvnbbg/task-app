# task-app
Simple task management app in PHP enhanced with home blood pressure (BP) tracking. Integrates features from Harvard Health: https://www.health.harvard.edu/heart-health/track-your-blood-pressure-at-home-the-right-way. Deployed on railway task-app-production-099b.up.railway.app (his documentation [https://railpack.com/
](https://railpack.com/))
## Features
- Create tasks
- Update tasks
- Delete tasks
- Mark tasks as completed

## Installation
1. Clone the repository: `git clone https://github.com/kvnbbg/task-app.git`
2. Navigate to the project directory: `cd task-app`
3. Install Railpack on Mac or Linux from GH releases (or install with another method). Windows is not supported yet. https://railpack.com/getting-started
Terminal window
curl -sSL https://railpack.com/install.sh | sh

Confirm that Railpack is installed correctly:
Terminal window
railpack --help
4. Before building, you need to have a BuildKit instance running and available through the BUILDKIT_HOST environment variable. The easiest way to do this is to run a BuildKit instance as a container:
Terminal window
mise run setup

Now you can build your image using Railpack:
Terminal window
railpack build ./path/to/project

(there are many examples in the Railpack repo that you can test with)




## Usage
1. Start the application: `start`
2. Open your browser and go to `http://localhost:8000`
3. Use the app to manage your tasks

## Contributing
Contributions are welcome! Please follow these steps:
1. Fork the repository
2. Create a new branch: `git checkout -b feature/your-feature`
3. Make your changes
4. Commit your changes: `git commit -m 'Add your commit message'`
5. Push to the branch: `git push origin feature/your-feature`
6. Submit a pull request

## License
This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.
