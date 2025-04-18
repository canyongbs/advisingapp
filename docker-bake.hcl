group "default" {
    targets = ["base", "cli"]
}

target "base" {
    dockerfile = "Dockerfile.base"
    tags = ["advisingapp-base:latest"]
}

target "cli" {
    dockerfile = "Dockerfile.cli"
    tags = ["advisingapp-cli:latest"]
    contexts = {
        "baseimage" = "target:base"
    }
}