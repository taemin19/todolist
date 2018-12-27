# Contributing

As a contributor, here are the guidelines we would like you to follow:

 - [Submission Guidelines](#submit)
 - [Coding Rules](#rules)
 - [Commit Message Guidelines](#commit)

## <a name="submit"></a> Submission Guidelines

### <a name="submit-issue"></a> Submitting an Issue

Do not open issues for general support questions as we want to keep GitHub issues for bug reports and feature requests.
Before you submit an issue, please search the issue tracker,
maybe an issue for your problem already exists and the discussion might inform you of workarounds readily available.

#### <a name="submit-issue-bug"></a> Fix a Bug
If you find a bug in the source code, open an issue and outline the bug.
After, you can [submit a Pull Request](#submit-pr) with a fix.

#### <a name="submit-issue-feature"></a> Add a Feature
Please consider what kind of change it is:

* For a **Major Feature**, first open an issue and outline your proposal so that it can be discussed.
This will also allow us to better coordinate our efforts, prevent duplication of work,
and help you to craft the change so that it is successfully accepted into the project.
After, [submit a Pull Request](#submit-pr).
* **Small Features** can be crafted and directly [submitted as a Pull Request](#submit-pr).

### <a name="submit-pr"></a> Submitting a Pull Request (PR)
Before you submit your Pull Request (PR) consider the following guidelines:

1. Search [GitHub](https://github.com/taemin19/todolist/pulls) for an open or closed PR
   that relates to your submission. You don't want to duplicate effort.
2. Be sure that an issue describes the problem you're fixing, or documents the feature you'd like to add.
3. Make your changes in a new git branch:

     ```shell
     git checkout -b my-pr-branch
     ```

4. Add your code, **including appropriate test cases**.
5. Follow our [Coding Rules](#rules).
6. Ensure that all tests pass.
7. Commit your changes using a descriptive commit message that follows our
  [commit message conventions](#commit).

     ```shell
     git commit
     ```

8. Push your branch to GitHub:

    ```shell
    git push origin my-pr-branch
    ```

9. In GitHub, create a new PR and compare changes between your pushed branch and `base:master` branch.
* Add the related issue in the PR description `Issue #1`.
* Before merging the PR, verify all CI checks have passed.

#### After your pull request is merged

After your pull request is merged, you can pull the changes from the main (upstream) repository:

* Check out the master branch:

    ```shell
    git checkout master
    ```

* Update your master with the latest upstream version:

    ```shell
    git pull origin master
    ```

## <a name="rules"></a> Coding Rules
To ensure consistency throughout the source code, keep these rules in mind as you are working:

* Your code should follow the [Symfony coding stantards](https://symfony.com/doc/current/contributing/code/standards.html).
* Symfony coding standards are based on the PSR-1, PSR-2 and PSR-4 standards.
* Check that the coding standards are followed, and use [php-cs-fixer](http://cs.sensiolabs.org/) to fix inconsistencies.
* All features or bug fixes **must be tested** by one or more specs (unit-tests/functional-tests).

## <a name="commit"></a> Commit Message Guidelines

There are rules over how our git commit messages can be formatted. This leads to **more
readable messages** that are easy to follow when looking through the **project history**.

### Commit Message Format
Each commit message consists of a **header** and a **body**.
The **header** is mandatory and the **body** is optional.

The header has a special format that includes a **type** and a **subject**:

```
<type>: <subject>
<BLANK LINE>
<body>
```

Any line of the **header** message cannot be longer 100 characters! This allows the message to be easier
to read on GitHub as well as in various git tools.

Samples:

```
docs: add a contributing file
```
```
build: update Symfony 3.1 to 3.4

- Support for Symfony 3.1 ended, upgrade to a maintained version
```

### Revert
If the commit reverts a previous commit, it should begin with `revert: `, followed by the header of the reverted commit.
In the body it should say: `This reverts commit <hash>.`, where the hash is the SHA of the commit being reverted.

### Type
Samples:

* **build**: Changes that affect the build system or external dependencies
* **ci**: Changes to our CI configuration files and scripts
* **docs**: Documentation only changes
* **feat**: A new feature
* **fix**: A bug fix
* **perf**: A code change that improves performance
* **refactor**: A code change that neither fixes a bug nor adds a feature
* **style**: Changes that do not affect the meaning of the code (white-space, formatting, missing semi-colons, etc)
* **test**: Adding missing tests or correcting existing tests

### Subject
The subject contains a succinct description of the change:

* use the imperative, present tense: "change" not "changed" nor "changes"
* don't capitalize the first letter
* no dot (.) at the end

### Body
Just as in the **subject**, use the imperative, present tense: "change" not "changed" nor "changes".
The body should include the motivation for the change and contrast this with previous behavior.
