package main

import (
	"go-otel-example/handlers"

	"github.com/gin-gonic/gin"
)

func main() {
	r := gin.Default()

	r.GET("/ping", handlers.Ping)
	r.GET("/rolldice", handlers.RollDice)
	r.GET("/jokes", handlers.Jokes)

	r.Run() // listen and serve on 0.0.0.0:8080
}
